
<?php
require 'config.php';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $ratings = [];
  for ($i = 1; $i <= 10; $i++) {
    $key = "tool$i";
    if (!isset($_POST[$key]) || !in_array($_POST[$key], ['1','2','3','4','5'])) {
      die("Invalid input for $key.");
    }
    $ratings[$key] = (int) $_POST[$key];
  }

  $stmt = $conn->prepare("INSERT INTO tool_ratings (tool1, tool2, tool3, tool4, tool5, tool6, tool7, tool8, tool9, tool10)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiiiiiiiii", ...array_values($ratings));
  $stmt->execute();
  $stmt->close();
}

$tools = [
  "HTML5", "CSS", "JavaScript", "Bootstrap", "PHP",
  "MySQL", "XAMPP/MAMP", "GazeParser", "DOM Manipulation", "Form Validation"
];

$sql = "SELECT " . implode(", ", array_map(fn($i) => "SUM(tool$i) AS sum$i, AVG(tool$i) AS avg$i", range(1, 10))) . " FROM tool_ratings";
$result = $conn->query($sql);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>InfoTech Tool Feedback Summary</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      padding: 2rem;
      background-color: #f8f9fa;
      font-family: 'Open Sans', sans-serif;
    }
    .summary-box {
      border: 1px solid #ccc;
      padding: 20px;
      max-width: 500px;
      margin: 0 auto;
      background: #fff;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="index.html">HCI Resources</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.html">Home</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" id="infoDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Info Tech
        </a>
        <div class="dropdown-menu" aria-labelledby="infoDropdown">
          <a class="dropdown-item" href="form.html">Form</a>
          <a class="dropdown-item active" href="results.php">Results</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="interests.html">Interests</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="evaluation.html">Evaluation Report</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="about.html">About</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
  <p class="text-muted mb-3">You are here: <strong>Info Tech → Results</strong></p>
  <h2 class="mb-4">Tool Rating Summary</h2>
  <div class="summary-box">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Tool</th>
          <th>SUM</th>
          <th>AVE</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($data) {
          for ($i = 1; $i <= 10; $i++) {
            $tool = htmlspecialchars($tools[$i - 1]);
            $sum = (int) $data["sum$i"];
            $avg = number_format($data["avg$i"], 2);
            echo "<tr>
                    <td><b>$tool</b></td>
                    <td><b>$sum</b></td>
                    <td><b>$avg</b></td>
                  </tr>";
          }
        } else {
          echo "<tr><td colspan='3'>No data found.</td></tr>";
        }
        ?>
      </tbody>
    </table>
    <a href="form.html" class="btn btn-secondary mt-3">Back to Form</a>
  </div>
</div>
</body>
</html>
