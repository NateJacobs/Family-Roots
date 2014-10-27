# Person Query

## Description
Search all the people in the TNG database.

## Methods

* get_results() - return an array of all the families found from the query.
* get_total() - the number of families found if count_total is set to TRUE.

## Parameters

### Include & Exclude
Show specific people by person ID.

* include (array) - list of people to include
* exclude (array) - list of people to exclude

#### Return specific familes
````$person_query = new TNG_Person_Query(['include' => [1, 2, 3]]);````

#### Return specific familes except a specific list of families
````$person_query = new TNG_Person_Query(['exclude' => [4, 5, 6]]);````

### Search
Search people.

* search (string) - Searches for possible string matches on columns.
* search_columns (array) - List of database columns to search with the search string.
	* ID - Search by person ID. This can be either a string (I123) or an integer (123).
	* first_name - Search by first name.
	* last_name - Search by last name.
	* birth_place - Search by birth place.
	* death_place - Search by death place.
	* burial_place - Search by burial place.
	
#### Dispaly people based upon a keyword search using first and last name
````$person_query = new TNG_Person_Query(['search' => 'John']);````

#### Dispaly familes based only upon the birth_place column
````$person_query = new TNG_Person_Query(['search' => 'New York', 'search_columns' => ['birth_place']]);````

### Date Searching
You can search the birth date, death date and burial date for all people by using the WP_Date_Query class arguments.

* date_search (array) - The array of date arguments. It supports all the values from the WP_Date_Query class.
* date_columns (array) - Which date columns to search: birth_date, death_date or burial_date. You can specify just one or all three.

### Fields
Dictate which fields are returned from the query. If 'all' is selected then the return array will be comprised of TNG_Person objects. If not 'all', then just the field requested will be returned.

* first_name
* last_name
* birth_place
* birth_date
* death_place
* death_date
* burial_place
* burial_date

### Order, Orderby, Offset, Number
Manipulate how the data is returned.

* order (string) - ASC or DESC.
* orderby (string) - which database column to order by.
* offset (integer) - which database row to start returning results from.
* number (integer) - the total number of people to return.

### Total Number of People Found

* count_total (bool) - Should the total number of people found be returned.