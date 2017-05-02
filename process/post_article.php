<?


	include $_SERVER["DOCUMENT_ROOT"]."/library/functions.php";

	$conn = init_sql();

    $stmt = $conn->prepare("INSERT INTO sessions (session, wikipedia) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST["session"], $_POST["title"]);
    $stmt->execute();

    if ($stmt->insert_id == 0) {

        $output["message"] = "Failed";

    } else {

        $output["message"] = "Success";
        $output["id"] = $stmt->insert_id;

        echo $stmt->insert_id;

    }

    $stmt->close();

    $conn->close();


?>