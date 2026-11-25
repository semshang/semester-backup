#!/bin/bash

# ============================================================
# Program: blackjack.sh
# Student ID: 20251145
#
# Purpose:
#   Plays a simplified multi-player game of Blackjack against
#   a computer-controlled dealer.
#
# Usage:
#   ./blackjack.sh
#
# Input:
#   - Number of players
#   - Player names
#   - Hit or stand decisions
#   - Replay decision
#
# Output:
#   - Cards dealt
#   - Hand values
#   - Blackjack/bust status
#   - Dealer actions
#   - Final results
#
# Requirements:
#   Bash shell
# ============================================================


# ------------------------------------------------------------
# GLOBAL ARRAYS AND VARIABLES
# ------------------------------------------------------------

declare -a deck
declare -a player_names
declare -a player_hands
declare -a player_status
declare -a dealer_hand

deck_position=0


# ------------------------------------------------------------
# FUNCTION: create_deck
# Creates a standard 52-card deck.
# ------------------------------------------------------------

create_deck()
{
    deck=()

    local suits=("Hearts" "Diamonds" "Clubs" "Spades")
    local ranks=("A" "2" "3" "4" "5" "6" "7" "8" "9" "10" "J" "Q" "K")

    local suit
    local rank

    for suit in "${suits[@]}"
    do
        for rank in "${ranks[@]}"
        do
            deck+=("${rank}_${suit}")
        done
    done
}


# ------------------------------------------------------------
# FUNCTION: shuffle_deck
#
# Uses the Fisher-Yates algorithm to shuffle the array.
# Cards are then dealt sequentially from the shuffled deck.
# Therefore, a dealt card cannot be dealt again.
# ------------------------------------------------------------

shuffle_deck()
{
    local i
    local j
    local temp

    for ((i = ${#deck[@]} - 1; i > 0; i--))
    do
        j=$((RANDOM % (i + 1)))

        temp="${deck[i]}"
        deck[i]="${deck[j]}"
        deck[j]="$temp"
    done

    deck_position=0
}


# ------------------------------------------------------------
# FUNCTION: format_card
# Converts internal card representation into readable text.
# Example: K_Hearts -> K of Hearts
# ------------------------------------------------------------

format_card()
{
    local card="$1"
    local rank="${card%%_*}"
    local suit="${card#*_}"

    REPLY="$rank of $suit"
}


# ------------------------------------------------------------
# FUNCTION: deal_card
#
# Returns the next card from the shuffled deck and advances
# the deck position so that card cannot be used again.
# ------------------------------------------------------------

deal_card()
{
    if (( deck_position >= ${#deck[@]} )); then
        echo "ERROR: No cards remain in the deck."
        exit 1
    fi

    REPLY="${deck[deck_position]}"
    ((deck_position++))
}


# ------------------------------------------------------------
# FUNCTION: calculate_hand
#
# Calculates the best valid value for a hand.
# Aces initially count as 11. If the hand exceeds 21,
# Aces are reduced from 11 to 1 as required.
# ------------------------------------------------------------

calculate_hand()
{
    local hand_string="$1"

    local total=0
    local aces=0
    local card
    local rank

    for card in $hand_string
    do
        rank="${card%%_*}"

        case "$rank" in

            A)
                total=$((total + 11))
                aces=$((aces + 1))
                ;;

            J|Q|K)
                total=$((total + 10))
                ;;

            *)
                total=$((total + rank))
                ;;
        esac
    done

    while (( total > 21 && aces > 0 ))
    do
        total=$((total - 10))
        aces=$((aces - 1))
    done

    REPLY="$total"
}


# ------------------------------------------------------------
# FUNCTION: count_cards
# Counts how many cards are present in a hand.
# ------------------------------------------------------------

count_cards()
{
    local hand_string="$1"
    local count=0
    local card

    for card in $hand_string
    do
        ((count++))
    done

    REPLY="$count"
}


# ------------------------------------------------------------
# FUNCTION: display_hand
# Displays all cards belonging to a hand.
# ------------------------------------------------------------

display_hand()
{
    local hand_string="$1"
    local card

    for card in $hand_string
    do
        format_card "$card"
        printf "[%s] " "$REPLY"
    done

    echo
}


# ------------------------------------------------------------
# FUNCTION: get_player_count
# Requires at least one player.
# Maximum is limited to four to keep the game manageable.
# ------------------------------------------------------------

get_player_count()
{
    while true
    do
        read -r -p "Enter number of players (1-4): " player_count

        if [[ $player_count =~ ^[0-9]+$ ]] &&
           (( player_count >= 1 && player_count <= 4 )); then
            return
        else
            echo "ERROR: Enter a whole number between 1 and 4."
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: get_player_names
# Obtains and validates each player's name.
# ------------------------------------------------------------

get_player_names()
{
    local i
    local name

    for ((i = 0; i < player_count; i++))
    do
        while true
        do
            read -r -p "Enter name for Player $((i + 1)): " name

            if [[ $name =~ ^[A-Za-z][A-Za-z[:space:]\'-]*$ ]]; then
                player_names[i]="$name"
                break
            else
                echo "ERROR: Enter a valid player name."
            fi
        done
    done
}


# ------------------------------------------------------------
# FUNCTION: initial_deal
#
# Gives two cards to every player and two cards to the dealer.
# ------------------------------------------------------------

initial_deal()
{
    local i
    local round

    player_hands=()
    player_status=()
    dealer_hand=()

    for ((i = 0; i < player_count; i++))
    do
        player_hands[i]=""
        player_status[i]="playing"
    done

    for ((round = 1; round <= 2; round++))
    do
        for ((i = 0; i < player_count; i++))
        do
            deal_card
            player_hands[i]+="$REPLY "
        done

        deal_card
        dealer_hand+=("$REPLY")
    done
}


# ------------------------------------------------------------
# FUNCTION: show_initial_hands
#
# Player cards are displayed face up.
# Only one dealer card is displayed initially.
# ------------------------------------------------------------

show_initial_hands()
{
    local i

    echo
    echo "================ INITIAL DEAL ================"

    for ((i = 0; i < player_count; i++))
    do
        echo
        echo "${player_names[i]}'s hand:"
        display_hand "${player_hands[i]}"

        calculate_hand "${player_hands[i]}"
        echo "Current value: $REPLY"
    done

    echo
    echo "Dealer's hand:"

    format_card "${dealer_hand[0]}"
    echo "[$REPLY] [Hidden Card]"

    echo "=============================================="
}


# ------------------------------------------------------------
# FUNCTION: player_turn
# Handles hit/stand decisions for one player.
# ------------------------------------------------------------

player_turn()
{
    local index="$1"
    local choice
    local value
    local card_count

    echo
    echo "=============================================="
    echo "${player_names[index]}'s turn"
    echo "=============================================="

    while true
    do
        echo
        echo "Your hand:"
        display_hand "${player_hands[index]}"

        calculate_hand "${player_hands[index]}"
        value="$REPLY"

        echo "Hand value: $value"

        count_cards "${player_hands[index]}"
        card_count="$REPLY"

        # Natural Blackjack occurs with two cards totalling 21.
        if (( value == 21 && card_count == 2 )); then
            echo "BLACKJACK!"
            player_status[index]="blackjack"
            return
        fi

        # Five-card Charlie.
        if (( card_count >= 5 && value <= 21 )); then
            echo "Five-card Charlie!"
            player_status[index]="five-card"
            return
        fi

        if (( value > 21 )); then
            echo "BUST! ${player_names[index]} has exceeded 21."
            player_status[index]="bust"
            return
        fi

        if (( value == 21 )); then
            echo "21 reached. Automatically standing."
            player_status[index]="stand"
            return
        fi

        read -r -p "Hit or stand? (h/s): " choice

        case "$choice" in

            h|H|hit|Hit|HIT)

                deal_card

                player_hands[index]+="$REPLY "

                format_card "$REPLY"

                echo "Card dealt: [$REPLY]"
                ;;

            s|S|stand|Stand|STAND)

                player_status[index]="stand"
                echo "${player_names[index]} stands."
                return
                ;;

            *)

                echo "ERROR: Enter h for hit or s for stand."
                ;;
        esac
    done
}


# ------------------------------------------------------------
# FUNCTION: dealer_turn
#
# Dealer must hit below 17 and stand on 17 or higher.
# ------------------------------------------------------------

dealer_turn()
{
    local dealer_string
    local dealer_value

    echo
    echo "=============================================="
    echo "DEALER'S TURN"
    echo "=============================================="

    while true
    do
        dealer_string="${dealer_hand[*]}"

        echo
        echo "Dealer's hand:"
        display_hand "$dealer_string"

        calculate_hand "$dealer_string"
        dealer_value="$REPLY"

        echo "Dealer value: $dealer_value"

        if (( dealer_value > 21 )); then
            echo "Dealer BUSTS!"
            return
        fi

        if (( dealer_value < 17 )); then

            echo "Dealer must hit."

            deal_card
            dealer_hand+=("$REPLY")

            format_card "$REPLY"
            echo "Dealer receives: [$REPLY]"

        else

            echo "Dealer stands on $dealer_value."
            return
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: determine_results
# Compares every player's hand against the dealer.
# ------------------------------------------------------------

determine_results()
{
    local dealer_string="${dealer_hand[*]}"
    local dealer_value
    local dealer_card_count

    local i
    local player_value
    local player_card_count

    calculate_hand "$dealer_string"
    dealer_value="$REPLY"

    count_cards "$dealer_string"
    dealer_card_count="$REPLY"

    echo
    echo "=============================================="
    echo "                 FINAL RESULTS"
    echo "=============================================="

    echo
    echo "Dealer:"
    display_hand "$dealer_string"
    echo "Dealer value: $dealer_value"

    for ((i = 0; i < player_count; i++))
    do
        calculate_hand "${player_hands[i]}"
        player_value="$REPLY"

        count_cards "${player_hands[i]}"
        player_card_count="$REPLY"

        echo
        echo "----------------------------------------------"
        echo "${player_names[i]}:"
        display_hand "${player_hands[i]}"
        echo "Value: $player_value"

        if [[ "${player_status[i]}" == "bust" ]]; then

            echo "RESULT: LOSE - Player busted."

        elif (( player_value == 21 &&
                player_card_count == 2 &&
                dealer_value == 21 &&
                dealer_card_count == 2 )); then

            echo "RESULT: PUSH - Both have Blackjack."

        elif (( player_value == 21 &&
                player_card_count == 2 )); then

            echo "RESULT: WIN - BLACKJACK!"

        elif (( dealer_value == 21 &&
                dealer_card_count == 2 )); then

            echo "RESULT: LOSE - Dealer has Blackjack."

        elif [[ "${player_status[i]}" == "five-card" ]]; then

            echo "RESULT: WIN - Five-card Charlie!"

        elif (( dealer_value > 21 )); then

            echo "RESULT: WIN - Dealer busted."

        elif (( player_value > dealer_value )); then

            echo "RESULT: WIN"

        elif (( player_value < dealer_value )); then

            echo "RESULT: LOSE"

        else

            echo "RESULT: PUSH"
        fi
    done

    echo
    echo "=============================================="
}


# ------------------------------------------------------------
# FUNCTION: play_game
# Runs one complete game.
# ------------------------------------------------------------

play_game()
{
    create_deck
    shuffle_deck

    get_player_count
    get_player_names

    initial_deal
    show_initial_hands

    local i

    for ((i = 0; i < player_count; i++))
    do
        player_turn "$i"
    done

    dealer_turn
    determine_results
}


# ============================================================
# MAIN PROGRAM
# ============================================================

while true
do

    echo
    echo "================================================"
    echo "                  BLACKJACK"
    echo "================================================"
    echo "Try to beat the dealer without going over 21."
    echo

    play_game

    echo

    while true
    do
        read -r -p "Play again? (y/n): " replay

        case "$replay" in

            y|Y)
                echo
                echo "Starting a new game..."
                break
                ;;

            n|N)
                echo
                echo "Thanks for playing Blackjack."
                echo "Program terminated."
                exit 0
                ;;

            *)
                echo "ERROR: Please enter y or n."
                ;;
        esac
    done

done
