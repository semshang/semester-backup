#!/bin/bash

# ============================================================
# Program: bonus.sh
# Student ID: 20251145
#
# Purpose:
#   Calculates the monthly salary, sales bonus, estimated tax
#   and net salary for Mercedes-Benz salespeople.
#
# Input:
#   - Month
#   - Number of salespeople (3-20)
#   - Salesperson name
#   - Number of each Mercedes-Benz model sold
#
# Output:
#   - Total sales
#   - Bonus
#   - Gross salary
#   - Tax
#   - Net salary
#   - Alphabetically sorted salesperson report
#   - Saved text report
#
# Requirements:
#   Bash and bc
# ============================================================


# ------------------------------------------------------------
# CONSTANT VALUES
# Average vehicle prices supplied by the assignment.
# ------------------------------------------------------------

A_CLASS_PRICE=31095
B_CLASS_PRICE=33162
C_CLASS_PRICE=42537
E_CLASS_PRICE=54437
AMG_C65_PRICE=79660

BASIC_SALARY=2000


# ------------------------------------------------------------
# ARRAYS
# Each array stores information for every salesperson.
# ------------------------------------------------------------

declare -a names
declare -a total_sales
declare -a bonuses
declare -a gross_salaries
declare -a taxes
declare -a net_salaries


# ------------------------------------------------------------
# FUNCTION: get_month
# Accepts alphabetic month names only.
# ------------------------------------------------------------

get_month()
{
    while true
    do
        read -r -p "Enter month: " month

        if [[ $month =~ ^[A-Za-z]+$ ]]; then
            return
        else
            echo "ERROR: Month must contain letters only."
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: get_salesperson_count
# Assignment requires between 3 and 20 salespeople.
# ------------------------------------------------------------

get_salesperson_count()
{
    while true
    do
        read -r -p "Enter number of salespeople (3-20): " salesperson_count

        if [[ $salesperson_count =~ ^[0-9]+$ ]] &&
           (( salesperson_count >= 3 && salesperson_count <= 20 )); then

            return
        else
            echo "ERROR: Enter a whole number between 3 and 20."
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: get_name
# Validates salesperson names.
# Letters, spaces, apostrophes and hyphens are accepted.
# ------------------------------------------------------------

get_name()
{
    local value

    while true
    do
        read -r -p "Enter salesperson name: " value

        if [[ $value =~ ^[A-Za-z][A-Za-z[:space:]\'-]*$ ]]; then
            REPLY="$value"
            return
        else
            echo "ERROR: Enter a valid salesperson name."
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: get_quantity
# Accepts whole numbers greater than or equal to zero.
# ------------------------------------------------------------

get_quantity()
{
    local prompt_message="$1"
    local value

    while true
    do
        read -r -p "$prompt_message" value

        if [[ $value =~ ^[0-9]+$ ]]; then
            REPLY="$value"
            return
        else
            echo "ERROR: Enter a whole number of zero or greater."
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: calculate_bonus
#
# Bonus rules supplied by assignment:
#
# >= 200,000  -> 10,000
# >  300,000  -> 15,000
# >  400,000  -> 20,000
# >  500,000  -> 25,000
# >  650,000  -> 30,000
# ------------------------------------------------------------

calculate_bonus()
{
    local sales="$1"

    if (( sales > 650000 )); then
        REPLY=30000

    elif (( sales > 500000 )); then
        REPLY=25000

    elif (( sales > 400000 )); then
        REPLY=20000

    elif (( sales > 300000 )); then
        REPLY=15000

    elif (( sales >= 200000 )); then
        REPLY=10000

    else
        REPLY=0
    fi
}


# ------------------------------------------------------------
# FUNCTION: calculate_tax
#
# The assignment supplies annual tax bands:
#
# Personal allowance: 12,500
# Basic rate:         20%
# Higher rate:        40%
#
# Monthly salary is converted to annual salary.
# Annual tax is calculated progressively.
# It is then divided by 12 to obtain monthly tax.
# ------------------------------------------------------------

calculate_tax()
{
    local monthly_gross="$1"
    local annual_gross
    local annual_tax

    annual_gross=$(echo "$monthly_gross * 12" | bc)

    if (( annual_gross <= 12500 )); then

        annual_tax=0

    elif (( annual_gross <= 50000 )); then

        annual_tax=$(echo \
            "scale=2; ($annual_gross - 12500) * 0.20" | bc)

    else

        annual_tax=$(echo \
            "scale=2; (50000 - 12500) * 0.20 + ($annual_gross - 50000) * 0.40" \
            | bc)
    fi

    REPLY=$(echo "scale=2; $annual_tax / 12" | bc)
}


# ------------------------------------------------------------
# FUNCTION: bubble_sort
#
# Sorts salespeople alphabetically.
#
# IMPORTANT:
# The assignment specifically requires bubble sort.
# The Bash "sort" command is NOT used.
#
# When names are swapped, all associated values must also
# be swapped so each salesperson keeps the correct data.
# ------------------------------------------------------------

bubble_sort()
{
    local n="${#names[@]}"
    local i
    local j
    local temp
    local swapped

    for ((i = 0; i < n - 1; i++))
    do
        swapped=false

        for ((j = 0; j < n - i - 1; j++))
        do
            if [[ "${names[j],,}" > "${names[j+1],,}" ]]; then

                # Swap names
                temp="${names[j]}"
                names[j]="${names[j+1]}"
                names[j+1]="$temp"

                # Swap sales
                temp="${total_sales[j]}"
                total_sales[j]="${total_sales[j+1]}"
                total_sales[j+1]="$temp"

                # Swap bonuses
                temp="${bonuses[j]}"
                bonuses[j]="${bonuses[j+1]}"
                bonuses[j+1]="$temp"

                # Swap gross salaries
                temp="${gross_salaries[j]}"
                gross_salaries[j]="${gross_salaries[j+1]}"
                gross_salaries[j+1]="$temp"

                # Swap tax values
                temp="${taxes[j]}"
                taxes[j]="${taxes[j+1]}"
                taxes[j+1]="$temp"

                # Swap net salaries
                temp="${net_salaries[j]}"
                net_salaries[j]="${net_salaries[j+1]}"
                net_salaries[j+1]="$temp"

                swapped=true
            fi
        done

        # Stop early if no swaps occurred.
        if [[ $swapped == false ]]; then
            break
        fi
    done
}


# ------------------------------------------------------------
# FUNCTION: display_results
# Displays final sorted results.
# ------------------------------------------------------------

display_results()
{
    echo
    echo "=============================================================================================================="
    echo "                                  MERCEDES-BENZ SALES REPORT"
    echo "=============================================================================================================="
    echo "Month: $month"
    echo

    printf "%-18s %14s %12s %14s %12s %14s\n" \
        "Name" \
        "Sales" \
        "Bonus" \
        "Gross Salary" \
        "Tax" \
        "Net Salary"

    echo "--------------------------------------------------------------------------------------------------------------"

    for ((i = 0; i < salesperson_count; i++))
    do
        printf "%-18s £%13s £%11s £%13s £%11s £%13s\n" \
            "${names[i]}" \
            "${total_sales[i]}" \
            "${bonuses[i]}" \
            "${gross_salaries[i]}" \
            "${taxes[i]}" \
            "${net_salaries[i]}"
    done

    echo "=============================================================================================================="
}


# ------------------------------------------------------------
# FUNCTION: save_results
# Saves the final report to a text file.
# ------------------------------------------------------------

save_results()
{
    output_file="bonus_${month}.txt"

    {
        echo "MERCEDES-BENZ SALES REPORT"
        echo "Month: $month"
        echo

        printf "%-18s %14s %12s %14s %12s %14s\n" \
            "Name" \
            "Sales" \
            "Bonus" \
            "Gross Salary" \
            "Tax" \
            "Net Salary"

        echo "--------------------------------------------------------------------------------------------------------------"

        for ((i = 0; i < salesperson_count; i++))
        do
            printf "%-18s £%13s £%11s £%13s £%11s £%13s\n" \
                "${names[i]}" \
                "${total_sales[i]}" \
                "${bonuses[i]}" \
                "${gross_salaries[i]}" \
                "${taxes[i]}" \
                "${net_salaries[i]}"
        done

    } > "$output_file"

    echo
    echo "Results successfully saved to:"
    echo "$(pwd)/$output_file"
}


# ============================================================
# MAIN PROGRAM
# ============================================================

echo
echo "======================================================"
echo "        MERCEDES-BENZ SALESPERSON BONUS SYSTEM"
echo "======================================================"
echo


# Obtain month.
get_month


# Obtain number of salespeople.
get_salesperson_count


# ------------------------------------------------------------
# Collect data for every salesperson.
# ------------------------------------------------------------

for ((i = 0; i < salesperson_count; i++))
do

    echo
    echo "------------------------------------------------------"
    echo "Salesperson $((i + 1)) of $salesperson_count"
    echo "------------------------------------------------------"

    # Get salesperson name.
    get_name
    names[i]="$REPLY"


    # Get number of each vehicle model sold.

    get_quantity "Number of A Class cars sold: "
    a_quantity="$REPLY"

    get_quantity "Number of B Class cars sold: "
    b_quantity="$REPLY"

    get_quantity "Number of C Class cars sold: "
    c_quantity="$REPLY"

    get_quantity "Number of E Class cars sold: "
    e_quantity="$REPLY"

    get_quantity "Number of AMG C65 cars sold: "
    amg_quantity="$REPLY"


    # --------------------------------------------------------
    # Calculate total sales using the assignment's average
    # vehicle prices.
    # --------------------------------------------------------

    sales=$((
        a_quantity * A_CLASS_PRICE +
        b_quantity * B_CLASS_PRICE +
        c_quantity * C_CLASS_PRICE +
        e_quantity * E_CLASS_PRICE +
        amg_quantity * AMG_C65_PRICE
    ))

    total_sales[i]="$sales"


    # Calculate bonus.
    calculate_bonus "$sales"
    bonuses[i]="$REPLY"


    # Calculate gross monthly salary.
    gross=$((BASIC_SALARY + bonuses[i]))
    gross_salaries[i]="$gross"


    # Calculate monthly tax.
    calculate_tax "$gross"
    taxes[i]="$REPLY"


    # Calculate salary after tax.
    net=$(echo "scale=2; $gross - ${taxes[i]}" | bc)
    net_salaries[i]="$net"

done


# Sort results alphabetically using bubble sort.
bubble_sort


# Display final results.
display_results


# Save results to a file.
save_results


echo
echo "Program completed successfully."
