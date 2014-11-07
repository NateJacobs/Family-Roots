# Family Query

## Description
Search all the family groups in the TNG database. 

## Methods

* get_results() - return an array of all the families found from the query.
* get_total() - the number of families found if count_total is set to TRUE.

## Parameters

### Include & Exclude
Show specific families by family ID.

* include (array) - list of families to include
* exclude (array) - list of families to exclude

#### Return specific familes
````$family_query = new TNG_Family_Query(['include' => [1, 2, 3]]);````

#### Return specific familes except a specific list of families
````$family_query = new TNG_Family_Query(['exclude' => [4, 5, 6]]);````

### Search
Search families.

* search (string) - Searches for possible string matches on columns.
* search_columns (array) - List of database columns to search with the search string.
	* ID - Search by family ID. This can be either a string (F123) or an integer (123).
	* marriage_date - Search by marriage date. The format is 'YYYY-mm-dd'.
	* divorce_date - Search by divorce date. The format is 'YYYY-mm-dd'.
	* marriage_place - Search by marriage place.
	* divorce_place - Search by divorce place.
	* husband - Search by the husband ID. This can be either a string (I123) or an integer (123).
	* wife - Search by the wife ID. This can be either a string (I123) or an integer (123).
	
#### Display familes based upon a keyword search
````$family_query = new TNG_Family_Query(['search' => 'New York']);````

#### Display familes based only upon the marriage_date column
````$family_query = new TNG_Family_Query(['search' => '2000-01-01', 'search_columns' => ['marriage_date']]);````

### Parent and Children Search
Search the families by parent and/or children by ID or name.

* child_in (array) - an array of children IDs.
* parent_in (array) - an array of parent IDs.
* husband_in (array) - an array of husband IDs.
* wife_in (array) - an array of wife IDs.
* child_name (array) - a child's first and last name.
* parent_name (array) - a parent's first and last name.
* husband_name (array) - a husband's first and last name.
* wife_name (array) - a wife's first and last name.

#### Return the families with the following children
````$family_query = new TNG_Family_Query(['child_in' => [1, 3, 5]]);````

#### Return the families with the following husband
````$family_query = new TNG_Family_Query(['husband_name' => ['first' => 'John', 'last' => 'Smith']]);````

### Order, Orderby, Offset, Number
Manipulate how the data is returned.

* order (string) - ASC or DESC.
* orderby (string) - which database column to order by.
* offset (integer) - which database row to start returning results from.
* number (integer) - the total number of families to return.

### Total Number of Families Found

* count_total (bool) - Should the total number of families found be returned.
