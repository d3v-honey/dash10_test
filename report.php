<?php

/**
 * Use this file to output reports required for the SQL Query Design test.
 * An example is provided below. You can use the `asTable` method to pass your query result to,
 * to output it as a styled HTML table.
 */

$database = 'nba2019';
require_once('vendor/autoload.php');
require_once('include/utils.php');


/*
 * Example Query
 * -------------
 * Retrieve all team codes & names
 */
echo '<h1>Example Query</h1>';
$teamSql = "SELECT * FROM team";
$teamResult = query($teamSql);
// dd($teamResult);
echo asTable($teamResult);

/*
 * Report 1
 * --------
 * Produce a query that reports on the best 3pt shooters in the database that are older than 30 years old. Only 
 * retrieve data for players who have shot 3-pointers at greater accuracy than 35%.
 * 
 * Retrieve
 *  - Player name
 *  - Full team name
 *  - Age
 *  - Player number
 *  - Position
 *  - 3-pointers made %
 *  - Number of 3-pointers made 
 *
 * Rank the data by the players with the best % accuracy first.
 */
echo '<h1>Report 1 - Best 3pt Shooters</h1>';
// write your query here
$best_3_pointers = "SELECT roster.name as player_name, team.name as team_name, age, number, pos, ((3pt*100)/3pt_attempted) as accuracy, 3pt
    FROM player_totals
    INNER JOIN roster ON (roster.id = player_totals.player_id)
    INNER JOIN team ON (team.code = roster.team_code)
    WHERE age > 30
    HAVING accuracy > 35
    ORDER BY accuracy DESC";
$result_for_best3pointers = query($best_3_pointers);
// dd($result_for_best3pointers);
echo asTable($result_for_best3pointers);


/*
 * Report 2
 * --------
 * Produce a query that reports on the best 3pt shooting teams. Retrieve all teams in the database and list:
 *  - Team name
 *  - 3-pointer accuracy (as 2 decimal place percentage - e.g. 33.53%) for the team as a whole,
 *  - Total 3-pointers made by the team
 *  - # of contributing players - players that scored at least 1 x 3-pointer
 *  - of attempting player - players that attempted at least 1 x 3-point shot
 *  - total # of 3-point attempts made by players who failed to make a single 3-point shot.
 * 
 * You should be able to retrieve all data in a single query, without subqueries.
 * Put the most accurate 3pt teams first.
 */
echo '<h1>Report 2 - Best 3pt Shooting Teams</h1>';
// write your query here
$best_3pt_shooting_team = "SELECT team.name, 
    ROUND(SUM(((3pt*100)/3pt_attempted))/COUNT(team.code),2) as accuracy,
    SUM(3pt) as total_3pt_per_team,
    SUM(player_totals.3pt>0) as no_of_contributing_players,
    SUM(player_totals.3pt_attempted>0) as no_of_attempting_players,
    SUM(player_totals.3pt=0) as no_of_failed_players
    FROM team
    INNER JOIN roster ON (roster.team_code = team.code)
    INNER JOIN player_totals ON (player_totals.player_id = roster.id)
    GROUP BY team.code
    ORDER BY accuracy DESC";

$result_for_best3ptshootingteam = query($best_3pt_shooting_team);
echo asTable($result_for_best3ptshootingteam);
?>