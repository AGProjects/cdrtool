#!/bin/bash

# File containing your data
if [ -f normalize.txt ]; then
    data_file="normalize.txt"
else
    data_file="/var/log/cdrtool/normalize.txt"
fi

# Initialize variables
declare -A daily_minutes
total_minutes=0
total_days=0

# Extract the date and minutes from the data
while read -r line; do
    # Extract the date and minutes from each line using regex
    if [[ $line =~ ([0-9]{4}-[0-9]{2}-[0-9]{2})\ .*\ CDR\ normalized:\ .*\ ([0-9]+)\ minutes ]]; then
        date="${BASH_REMATCH[1]}"
        minutes="${BASH_REMATCH[2]}"

#        if [[ "$date" == "$(date +%Y-%m-%d)" ]]; then
#            continue
#        fi
                
        # Sum the minutes for each day
        daily_minutes["$date"]=$(( ${daily_minutes["$date"]} + minutes ))
    fi
done < "$data_file"

# Calculate the total minutes and total number of days
for date in "${!daily_minutes[@]}"; do
    total_minutes=$(( total_minutes + daily_minutes["$date"] ))
    total_days=$(( total_days + 1 ))
done

# Calculate the average minutes per day
if [ $total_days -gt 0 ]; then
    average_per_day=$(echo "scale=2; $total_minutes / ($total_days - 1)" | bc)
else
    average_per_day=0
fi

# Predict the total for the month (assuming the pattern remains constant)
predicted_monthly_total=$(echo "scale=0; $average_per_day * 30" | bc)

# Output the results
echo "Daily Totals:"
for date in "${!daily_minutes[@]}"; do
    echo "Date: $date Total minutes: ${daily_minutes["$date"]}"
done

average_per_day=$(printf "%.0f" "$average_per_day")
predicted_monthly_total=$(printf "%.0f" "$predicted_monthly_total")

echo ""
echo "Predicted monthly: $predicted_monthly_total minutes"
echo "Average per day: $average_per_day minutes/day"

