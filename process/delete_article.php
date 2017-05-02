<?


	include $_SERVER["DOCUMENT_ROOT"]."/library/functions.php";

	$conn = init_sql();


    $stmt = $conn->prepare("DELETE FROM sessions WHERE session = ? AND id = ?");
    $stmt->bind_param("si", $_POST["session"], $_POST["db"]);
    $stmt->execute();

    $stmt->close();

    $conn->close();


?>