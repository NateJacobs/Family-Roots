# Family Query

## Description
Search all the family groups in the TNG database. 

## Interacting with TNG_Family_Query

## Methods

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
	
#### Dispaly familes based upon a keyword search
````$family_query = new TNG_Family_Query(['search' => 'New York']);````

#### Dispaly familes based only upon the marriage_date column
````$family_query = new TNG_Family_Query(['search' => '2000-01-01', 'search_columns' => ['marriage_date']]);````