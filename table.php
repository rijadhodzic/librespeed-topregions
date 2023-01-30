<?php
include 'results/telemetry_settings.php';

// Connect to the librespeed database using the settings from the telemetry_settings.php file
$conn = mysqli_connect($MySql_hostname, $MySql_username, $MySql_password, $MySql_databasename, $MySql_port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Select the top 10 regions with the highest dl, ul, and smallest ping from the database
$sql = "SELECT region, AVG(dl) as dl, AVG(ul) as ul, AVG(ping) as ping 
        FROM (
            SELECT 
                JSON_EXTRACT(ispinfo, '$.rawIspInfo.region') as region,
                dl, ul, ping 
            FROM speedtest_users
        ) as temp 
        GROUP BY region 
        ORDER BY dl DESC, ul DESC, ping ASC 
        LIMIT 10";

$result = mysqli_query($conn, $sql);

echo "<div class='table-responsive'>
        <table class='table table-striped'>
            <thead>
                <tr>
                    <th>Region</th>
                    <th>DL (Mbps)</th>
                    <th>UL (Mbps)</th>
                    <th>Ping (ms)</th>
                </tr>
            </thead>
            <tbody>";

if (mysqli_num_rows($result) > 0) {
    // Output the data for each region in a table row
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . $row["region"]. "</td>
                <td>" . number_format((float)$row["dl"], 1, '.', '') . "</td>
                <td>" . number_format((float)$row["ul"], 1, '.', '') . "</td>
                <td>" . number_format((float)$row["ping"], 1, '.', '') . "</td>
            </tr>";
    }
    echo "</tbody>
        </table>
    </div>";
} else {
    echo "No results found.";
}

// Close the database connection
mysqli_close($conn);
?>
