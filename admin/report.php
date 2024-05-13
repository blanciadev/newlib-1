<?php
// Include the Composer autoloader to load PHPSpreadsheet classes
require '../vendor/autoload.php';

// Define a function to get unique visitors count for a given month
function getUniqueVisitorsCount($conn, $month) {
    $uniqueVisitorsQuery = "SELECT COUNT(DISTINCT Borrower_ID) AS UniqueVisitors 
                            FROM tbl_borrow 
                            WHERE MONTH(Date_Borrowed) = $month";
    $uniqueVisitorsResult = mysqli_query($conn, $uniqueVisitorsQuery);
    $uniqueVisitorsCount = ($uniqueVisitorsResult) ? mysqli_fetch_assoc($uniqueVisitorsResult)['UniqueVisitors'] : 0;
    return $uniqueVisitorsCount;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "root", "db_library_2", 3308);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create a new Spreadsheet object
$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

// Create a new worksheet
$sheet = $spreadsheet->getActiveSheet();

// Add headers for each section
$sheet->setCellValue('A1', 'Books Borrowed');
$sheet->setCellValue('F1', 'LOGS');
$sheet->setCellValue('K1', 'Fines');

// Initialize row counters for each section
$rowBooks = 2;
$rowLogs = 2;
$rowFines = 2;

// Query to count books borrowed per month
$countBooksQuery = "SELECT MONTH(Date_Borrowed) AS Month, COUNT(*) AS BooksBorrowed 
                    FROM tbl_borrow 
                    GROUP BY MONTH(Date_Borrowed)";
$countBooksResult = mysqli_query($conn, $countBooksQuery);

if ($countBooksResult) {
    // Loop through the result and add data to the Excel file
    while ($row = mysqli_fetch_assoc($countBooksResult)) {
        $month = date("F", mktime(0, 0, 0, $row['Month'], 1));
        $uniqueVisitorsCount = getUniqueVisitorsCount($conn, $row['Month']); // You need to implement this function to get unique visitors count

        // Add data to the Books Borrowed section
        $sheet->setCellValue('A' . $rowBooks, 'Month');
        $sheet->setCellValue('B' . $rowBooks, $month);
        $sheet->setCellValue('C' . $rowBooks, 'Books Borrowed');
        $sheet->setCellValue('D' . $rowBooks, $row['BooksBorrowed']);
        $sheet->setCellValue('E' . $rowBooks, 'Visitors');
        $sheet->setCellValue('F' . $rowBooks, $uniqueVisitorsCount);

        $rowBooks++;
    }
}

// Query to count unique Borrower_ID and Date & Time per month from tbl_log
$countLogsQuery = "SELECT MONTH(`Date_Time`) AS Month, 
                        COUNT(DISTINCT Borrower_ID) AS UniqueBorrowers, 
                        COUNT(*) AS TotalLogs 
                    FROM tbl_log 
                    GROUP BY MONTH(`Date_Time`)";
$countLogsResult = mysqli_query($conn, $countLogsQuery);

if ($countLogsResult) {
    // Loop through the result and add data to the Excel file
    while ($row = mysqli_fetch_assoc($countLogsResult)) {
        $month = date("F", mktime(0, 0, 0, $row['Month'], 1));

        // Add data to the LOGS section
        $sheet->setCellValue('F' . $rowLogs, 'Month');
        $sheet->setCellValue('G' . $rowLogs, $month);
        $sheet->setCellValue('H' . $rowLogs, 'Unique Borrowers');
        $sheet->setCellValue('I' . $rowLogs, $row['UniqueBorrowers']);
        $sheet->setCellValue('J' . $rowLogs, 'Total Logs');
        $sheet->setCellValue('K' . $rowLogs, $row['TotalLogs']);

        $rowLogs++;
    }
}

// Define the reasons for fines
$reasons = ["DAMAGE", "PARTIALLY DAMAGE", "GOOD CONDITION", "LOST"];

foreach ($reasons as $reason) {
    $totalFinesQuery = "SELECT MONTH(Date_Created) AS Month, 
                                SUM(Amount) AS TotalAmount,
                                COUNT(DISTINCT Borrower_ID) AS UniqueBorrowers 
                            FROM tbl_fines 
                            WHERE Payment_Date IS NOT NULL 
                            AND Reason = ? 
                            GROUP BY MONTH(Date_Created)";
    
    $stmt = $conn->prepare($totalFinesQuery);
    $stmt->bind_param("s", $reason);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Loop through the result and add data to the Excel file
        while ($row = $result->fetch_assoc()) {
            $month = date("F", mktime(0, 0, 0, $row['Month'], 1));

            // Add data to the Fines section
            $sheet->setCellValue('K' . $rowFines, 'Reason');
            $sheet->setCellValue('L' . $rowFines, $reason);
            $sheet->setCellValue('M' . $rowFines, 'Month');
            $sheet->setCellValue('N' . $rowFines, $month);
            $sheet->setCellValue('O' . $rowFines, 'Total Amount of Fines');
            $sheet->setCellValue('P' . $rowFines, $row['TotalAmount']);
            $sheet->setCellValue('Q' . $rowFines, 'Unique Borrowers');
            $sheet->setCellValue('R' . $rowFines, $row['UniqueBorrowers']);

            $rowFines++;
        }
    } else {
        // If no data found for a reason, add a message to the Excel file
        $sheet->setCellValue('K' . $rowFines, 'No data found for reason');
        $sheet->setCellValue('L' . $rowFines, $reason);

        $rowFines++;
    }
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="report.xlsx"');
header('Cache-Control: max-age=0');

// Save the Excel file to php://output
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');

// Close connection
mysqli_close($conn);
?>
