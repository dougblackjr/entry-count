# Entry Count

## PARAMETERS:

1) category - Optional. Allows you to specify category id number 
(the id number of each category is displayed in the Control Panel).
You can stack categories using pipe character to get entries 
with any of those categories, e.g. category="3|6|8". Or use "not" 
(with a space after it) to exclude categories, e.g. category="not 4|5|7".
Also you can use "&" symbol to get entries each of which was posted into all 
specified categories, e.g. category="3&6&8". 

2) category_group - Optional. Allows you to specify category group id number
(the id number of each category group is displayed in the Control Panel).
You can stack category groups using pipe character to get entries 
with any of those category groups, e.g. category_group="10|9|11". Or use "not" 
(with a space after it) to exclude categories, e.g. category_group="not 10|9|11".

3) channel - Optional. Allows you to specify channel name.
You can use the pipe character to get entries from any of those 
channels, e.g. channel="channel1|channel2|channel3".
Or you can add the word "not" (with a space after it) to exclude channels,
e.g. channel="not channel1|channel2|channel3".

4) author_id - Optional. Allows you to specify author id number.
You can use the pipe character to get entries posted by any of those 
authors, e.g. author_id="5|11|18".
Or you can add the word "not" (with a space after it) to exclude authors,
e.g. author_id="not 1|9". If you need to output number of entries by
logged-in user, use variable {logged_in_member_id} as the value of this
parameter.

5) author_group - Optional. Allows you to specify author group id number.
You can use the pipe character to get entries posted by any of those 
authors, e.g. author_id="1|2".
Or you can add the word "not" (with a space after it) to exclude authors,
e.g. author_id="not 1|2".

6) site - Optional. Allows you to specify site id number.
You can stack site id numbers using pipe character to get entries 
from any of those sites, e.g. site="1|3". Or use "not" 
(with a space after it) to exclude sites, e.g. site="not 1|2".

7) status - Optional. Allows you to specify status of entries.
You can stack statuses using pipe character to get entries 
having any of those statuses, e.g. status="open|draft". Or use "not" 
(with a space after it) to exclude statuses, 
e.g. status="not submitted|processing|closed".
4
8) url_title - Optional. Allows you to specify url_title of an entry.
You can stack url_titles using pipe character to get entries 
having any of those url_titles. Or use "not" 
(with a space after it) to exclude url_titles.

9) entry_id - Optional. Allows you to specify entry id number of an entry.
You can stack entry_ids using pipe character to get entries 
having any of those entry_ids. Or use "not" 
(with a space after it) to exclude entry_ids.

10) show_expired - Optional. Allows you to specify if you wish expired entries
to be counted. If the value is "yes", expired entries will be counted; if the
value is "no", expired entries will not be counted. Default value is "no".

11) show_future_entries - Optional. Allows you to specify if you wish future entries
to be counted. If the value is "yes", future entries will be counted; if the
value is "no", future entries will not be counted. Default value is "no".

12) invalid_input - Optional. Accepts two values: “alert” and “silence”.
Default value is “silence”. If the value is “alert”, then in cases when some
parameter’s value is invalid plugin exits and PHP alert is being shown;
if the value is “silence”, then in cases when some parameter’s value
is invalid plugin finishes its work without any alert being shown. 
Set this parameter to “alert” for development, and to “silence” - for deployment.

13) field_name - Optional. Used when there is a need to display entries
having certain custom field equal to or not equal to or like  
specific value.

14) field_value - Optional. Used when there is a need to display entries
having certain custom field equal to or not equal to or like specific value.
If you need to display entries having certain custom field empty or not empty,
use "IS_EMPTY" or "IS_NOT_EMPTY" as the value of this parameter.

15) field - Optional. Used when there is a need to display entries having certain custom field
 *not* equal or *like* to specific value. Acceps the value "include", "exclude" and "like" (only entries 
having field_name LIKE field_value will be displayed - SQL LIKE notation will be used). 
Default value is "include".

16) start_on_relative - Optional. You can specify a relative time (in seconds) on which to start counting the entries.
E.g. start_on_relative="86400" means that the counting of entries will start from those which were posted 86400 seconds (i.e. 24 hours) ago.

17) stop_before_relative - Optional. You can specify a relative time (in seconds) on which to end counting the entries.
E.g. stop_before_relative="43200" means that only thiose entries will be counted which were posted earlier than 43200 seconds (i.e. 12 hours) ago.

18) start_on - Optional. Allows you to specify the date starting from which the plugin 
should look for entries. The date/time must be specified in the following format:
YYYY-MM-DD HH:MM . Here, YYYY is the four-digit year, MM is the two-digit month, 
DD is the two-digit day of the month, HH is the two-digit hour of the day, 
and MM is the two-digit minute of the hour. If the month, day, hour or minute has only one digit, 
precede that digit with a zero. (E.g. "March 9, 2004" would become "2004-03-09".) 
All date/times are given in local time, according to your ExpressionEngine configuration.

19) stop_before - Optional. Allows you to specify the date before which the plugin 
should stop looking for entries. The date/time must be specified in the following format:
YYYY-MM-DD HH:MM . Here, YYYY is the four-digit year, MM is the two-digit month, 
DD is the two-digit day of the month, HH is the two-digit hour of the day, 
and MM is the two-digit minute of the hour. If the month, day, hour or minute has only one digit, 
precede that digit with a zero. (E.g. "March 9, 2004" would become "2004-03-09".) 
All date/times are given in local time, according to your ExpressionEngine configuration.

20) month - Optional. The number of month that you want to see entries for. 1 for January, 2 for February, etc.

21) year - Optional. Four digit year to find entries for.

## VARIABLES:

1) entry_count - outputs the number of entries which satisfy condition 
entered in prameters.

## EXAMPLE OF USAGE:

```
{exp:entry_count category="6" channel="not channel1|channel4" site="1"}
    {entry_count}
{/exp:entry_count}
```

The variable {entry_count} placed between {exp:entry_count} and {/exp:entry_count} tags
will output the number of entries which satisfy condition entered in prameters.

You can use {entry_count} variable in conditionals:

```
{exp:entry_count category="6" channel="not channel1|channel4" site="1"}
    {if entry_count==0}
        Some code
    {if:elseif entry_count==1}
        Some other code
    {if:else}
        Yet another code
    {/if}
{/exp:entry_count}
```

In contrast with "if no_results" conditional, which does not allow its parent tag {exp:channel:entries} to be
wrapped in a plugin, contionals inside {exp:entry_count} does not interfere with outer plugins. That is,
while the code as this 

```
{exp:category_id category_group="3" category_url_title="segment_3" parse="inward"}
    {exp:channel:entries channel="my_channel" category="{category_id}"}
        {if no_results}
            No entry found! 
        {/if}
    {/exp:channel:entries}
{/exp:category_id}
```

will not work, the code as this

```
{exp:category_id category_group="3" category_url_title="segment_3" parse="inward"}
    {exp:entry_count channel="my_channel" category="{category_id}"}
        {if entry_count==0}
            No entry found! 
        {/if}
    {/exp:entry_count}
{/exp:category_id}
```

will work properly.

If you need to find number of entries in which certain field has definite value, use the
code as this:
```
{exp:entry_count channel="my_channel" field_name="my_cust_field" field_value="mycustomvalue"}
    {entry_count}
{/exp:entry_count}
```