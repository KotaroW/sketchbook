/************************************************************************************************************************************
 *
 * Here is a situation where you have two tables, one of which is a meta data table, and one intersection table connecting those two.
 * In table_1 we only have meta data index (or key), not the meta value.
 * Now we want to get the "meta values" in the query result and each row in the result must be distinct by table_1 primary key.
 * Also every single data item in table_1 must appear in the result.
 * Last but not least table_1 items can have more than one meta values and we want to process them as an array later on.
 *
 *
 * A query result would look like this (essentials only):
 * +------------------------------------------------+
 * | table_1 field | table_2 (meta table) value(s)  |
 * +------------------------------------------------+
 * | field values  |  [value_1[, value_2 ...]       |
 * +------------------------------------------------+
 *
 * We thought the point of the argument was "where to get the meta value?", on the server-side or client side? We conclude that there
 * was no definite answer to that question and it really depends.
 ***********************************************************************************************************************************/


SELECT
	table_1.*, group_concat(distinct table_2.field)
FROM
	table_1
LEFT JOIN
	intersection_table
ON
	table_1.primary_key = intersection_table.foreign_key
LEFT JOIN
	table
ON
	intersection_table.foreign_key = table_2.primary_key
GROUP BY
	table_1.primary_key;
