#!/bin/bash

# ============================================================
# Program: area.sh
# Purpose:
#   Calculates the area of a rectangle using width and height
#   entered in centimetres. The result is displayed in square
#   centimetres, square metres and square inches.
#
# Usage:
#   ./area.sh
#
# Input:
#   Positive numeric width and height values in centimetres.
#
# Output:
#   Rectangle area in cm^2, m^2 and in^2.
#
# Requirements:
#   Bash shell and the bc utility.
# ============================================================

# Regular expression accepts positive integers and decimals.
number_regex='^[0-9]+([.][0-9]+)?$'

# Validate a numeric dimension.
get_dimension()
{
    local prompt_message="$1"
    local value

    while true
    do
        read -r -p "$prompt_message" value

        if [[ ! $value =~ $number_regex ]]; then
            echo "ERROR: Enter a valid positive number, for example 12 or 12.5."
        elif (( $(echo "$value <= 0" | bc -l) )); then
            echo "ERROR: The value must be greater than zero."
        else
            REPLY="$value"
            return
        fi
    done
}

# Calculate and display the rectangle areas.
calculate_area()
{
    local width="$1"
    local height="$2"

    local area_cm
    local area_m
    local area_in

    area_cm=$(echo "scale=4; $width * $height" | bc)
    area_m=$(echo "scale=6; $area_cm / 10000" | bc)
    area_in=$(echo "scale=4; $area_cm / (2.54 * 2.54)" | bc)

    echo
    echo "----------------------------------------"
    echo "        RECTANGLE AREA RESULTS"
    echo "----------------------------------------"
    echo "Width:              $width cm"
    echo "Height:             $height cm"
    echo "Area in cm^2:       $area_cm"
    echo "Area in m^2:        $area_m"
    echo "Area in square in:  $area_in"
    echo "----------------------------------------"
}

# Main program loop.
while true
do
	echo
	echo "===================================="
    echo "       RECTANGLE AREA CALCULATOR"
    echo "========================================"
    echo

    get_dimension "Enter rectangle width in cm: "
    width="$REPLY"
    get_dimension "Enter rectangle height in cm: "
    height="$REPLY"

    calculate_area "$width" "$height"

    echo
    while true
    do
        read -r -p "Calculate another rectangle? (y/n): " choice

        case "$choice" in
            y|Y)
                break
                ;;
            n|N)
                echo "Program terminated."
                exit 0
                ;;
            *)
                echo "ERROR: Please enter y or n."
                ;;
        esac
    done
done
