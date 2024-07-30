

<?php
// MySQL 데이터베이스 연결 정보
$servername = "localhost";
$username = "root";
$password = "tjrwls0802";
$dbname = "ticket";

// 데이터베이스 연결 생성
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST 데이터 가져오기
    $performance_id = $_POST['performance_id'];
    $selected_seats = $_POST['seat_number'];

    // performance_information에서 데이터 가져오기
    $sql = "SELECT event_date, event_name, event_cost FROM performance_information WHERE performance_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $performance_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 데이터가 있는 경우
        $row = $result->fetch_assoc();
        $event_date = $row['event_date'];
        $event_name = $row['event_name'];
        $event_cost = $row['event_cost'];

        foreach ($selected_seats as $seat_number) {
            // ticket_information에 데이터 삽입
            $sql_insert = "INSERT INTO ticket_information (performance_id, event_date, event_name, event_cost, seat_number) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("issdi", $performance_id, $event_date, $event_name, $event_cost, $seat_number);

            if ($stmt_insert->execute()) {
                echo "축하합니다! 티켓 구매에 성공했습니다!!<br><br>";
                echo "공연 이름 : " . htmlspecialchars($event_name) . "<br>";
                echo "공연 날짜 : " . htmlspecialchars($event_date) . "<br>";
                echo "공연 비용 : " . htmlspecialchars($event_cost) . "<br>";
                echo "공연 좌석 : " . htmlspecialchars($seat_number) . "<br>";
            } else {
                echo "Error: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }
    } else {
        echo "Event not found.";
    }

    $stmt->close();
}

$conn->close();
?>