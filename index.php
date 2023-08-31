<?php

try {
    $conn = new PDO("mysql:host=localhost;dbname=tables", 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Sayfa başına öğe sayısı
    $itemsPerPage = 10;

    // Geçerli sayfa numarasını al
    $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

    // OFFSET hesaplanır
    $offset = ($currentPage - 1) * $itemsPerPage;

    // Toplam veri sayısını al
    $totalCount = $conn->query("SELECT COUNT(*) FROM `veriler`")->fetchColumn();

    // Toplam sayfa sayısı
    $totalPages = ceil($totalCount / $itemsPerPage);

    // Veritabanından verileri çek
    $sql = "SELECT `ID`, `ad`, `soyad`, `mail`, `yas`, `okul` FROM `veriler` LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Veritabanı bağlantısı kapatılır
    $conn = null;
} catch (PDOException $e) {
    echo "Veritabanı hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .pagination {
            margin-top: 10px;
        }

        .pagination a {
            margin-right: 5px;
            text-decoration: none;
            color: #333;
        }

        .pagination .active {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Mail</th>
            <th>Yaş</th>
            <th>Okul</th>
        </tr>
        <?php foreach ($result as $row) : ?>
        <tr>
            <td><?php echo $row["ID"]; ?></td>
            <td><?php echo $row["ad"]; ?></td>
            <td><?php echo $row["soyad"]; ?></td>
            <td><?php echo $row["mail"]; ?></td>
            <td><?php echo $row["yas"]; ?></td>
            <td><?php echo $row["okul"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Sayfalama bağlantıları -->
    <div class="pagination">
        <?php
    $visiblePageCount = 5; // Görüntülenecek sayfa sayısı
    $halfVisible = floor($visiblePageCount / 2);

    $startPage = max(1, $currentPage - $halfVisible);
    $endPage = min($totalPages, $currentPage + $halfVisible);

    if ($currentPage > 1) {
        echo "<a href=\"?page=1\">İlk</a> ";
        $prevPage = $currentPage - 1;
        echo "<a href=\"?page=$prevPage\">Önceki</a> ";
    } else {
        echo "<span class=\"disabled\">İlk</span> ";
        echo "<span class=\"disabled\">Önceki</span> ";
    }

    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = $i === $currentPage ? 'active' : '';
        echo "<a class=\"$activeClass\" href=\"?page=$i\">$i</a> ";
    }

    if ($currentPage < $totalPages) {
        $nextPage = $currentPage + 1;
        echo "<a href=\"?page=$nextPage\">Sonraki</a> ";
        echo "<a href=\"?page=$totalPages\">Son</a>";
    } else {
        echo "<span class=\"disabled\">Sonraki</span> ";
        echo "<span class=\"disabled\">Son</span>";
    }
    ?>
    </div>

</body>

</html>
