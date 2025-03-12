<?php
session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

try {
    // Connect to (or create) the products database
    $db = new PDO('sqlite:market.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the products table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        price REAL NOT NULL,
        description TEXT,
        image TEXT,
        stock INTEGER NOT NULL
    )");

    // Insert the 50 default products if the table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $defaultProducts = [
            1  => ['name' => 'Hammer', 'price' => 10, 'description' => 'A sturdy hammer for all your construction needs.', 'image' => 'images/Hammer.jpg'],
            2  => ['name' => 'Screwdriver Set', 'price' => 15, 'description' => 'A complete set of screwdrivers for all screw types.', 'image' => 'images/Screwdriver Set.jpg'],
            3  => ['name' => 'Wrench', 'price' => 20, 'description' => 'A durable wrench for tightening bolts and nuts.', 'image' => 'images/Wrench.jpg'],
            4  => ['name' => 'Tape Measure', 'price' => 12, 'description' => 'A flexible tape measure for precise measurements.', 'image' => 'images/Tape Measure.jpg'],
            5  => ['name' => 'Level', 'price' => 25, 'description' => 'A spirit level for accurate alignment of objects.', 'image' => 'images/level.jpg'],
            6  => ['name' => 'Utility Knife', 'price' => 18, 'description' => 'A multipurpose utility knife for cutting materials.', 'image' => 'images/utility knife.jpg'],
            7  => ['name' => 'Safety Gloves', 'price' => 8, 'description' => 'Protective gloves for hand safety during work.', 'image' => 'images/safety gloves.jpg'],
            8  => ['name' => 'Drill', 'price' => 50, 'description' => 'A powerful drill for heavy-duty tasks.', 'image' => 'images/drill.jpg'],
            9  => ['name' => 'Chainsaw', 'price' => 100, 'description' => 'A chainsaw for cutting through wood with ease.', 'image' => 'images/chainsaw.jpg'],
            10 => ['name' => 'Circular Saw', 'price' => 80, 'description' => 'A circular saw for precise cuts.', 'image' => 'images/circular saw.jpg'],
            11 => ['name' => 'Angle Grinder', 'price' => 60, 'description' => 'An angle grinder for cutting and polishing.', 'image' => 'images/angle grinder.jpg'],
            12 => ['name' => 'Sander', 'price' => 45, 'description' => 'A sander for smooth finishing.', 'image' => 'images/sander.jpg'],
            13 => ['name' => 'Power Washer', 'price' => 120, 'description' => 'A power washer for deep cleaning.', 'image' => 'images/power washer.jpg'],
            14 => ['name' => 'Paint Sprayer', 'price' => 70, 'description' => 'A paint sprayer for even coats.', 'image' => 'images/paint sprayer.jpg'],
            15 => ['name' => 'Ladder', 'price' => 30, 'description' => 'A sturdy ladder for reaching high places.', 'image' => 'images/ladder.jpg'],
            16 => ['name' => 'Toolbox', 'price' => 40, 'description' => 'A toolbox to organize your tools.', 'image' => 'images/toolbox.jpg'],
            17 => ['name' => 'Work Light', 'price' => 22, 'description' => 'A bright work light for low-light conditions.', 'image' => 'images/work light.jpg'],
            18 => ['name' => 'Dust Mask', 'price' => 5, 'description' => 'A dust mask to protect from particles.', 'image' => 'images/dust mask.jpg'],
            19 => ['name' => 'Safety Goggles', 'price' => 7, 'description' => 'Protective goggles to shield your eyes.', 'image' => 'images/safety goggles.jpg'],
            20 => ['name' => 'Ear Protection', 'price' => 6, 'description' => 'Ear plugs to protect your hearing.', 'image' => 'images/ear protection.jpg'],
            21 => ['name' => 'Tool Belt', 'price' => 25, 'description' => 'A belt to carry your essential tools.', 'image' => 'images/tool belt.jpg'],
            22 => ['name' => 'Stud Finder', 'price' => 15, 'description' => 'A stud finder to locate wall studs.', 'image' => 'images/stud finder.jpg'],
            23 => ['name' => 'Pipe Wrench', 'price' => 18, 'description' => 'A pipe wrench for plumbing tasks.', 'image' => 'images/pipe wrench.jpg'],
            24 => ['name' => 'Pliers', 'price' => 12, 'description' => 'Pliers for gripping and twisting.', 'image' => 'images/pliers.jpg'],
            25 => ['name' => 'Allen Wrench Set', 'price' => 10, 'description' => 'A set of Allen wrenches for hex bolts.', 'image' => 'images/allen wrench.jpg'],
            26 => ['name' => 'Bolt Cutter', 'price' => 20, 'description' => 'Bolt cutters for heavy-duty cutting.', 'image' => 'images/bolt cutter.jpg'],
            27 => ['name' => 'Multimeter', 'price' => 35, 'description' => 'A digital multimeter for electrical measurements.', 'image' => 'images/multimeter.jpg'],
            28 => ['name' => 'Voltage Tester', 'price' => 8, 'description' => 'A voltage tester for checking electrical circuits.', 'image' => 'images/voltage tester.jpg'],
            29 => ['name' => 'Circuit Breaker Finder', 'price' => 18, 'description' => 'Find your circuit breakers easily.', 'image' => 'images/circuit breaker.jpg'],
            30 => ['name' => 'Heat Gun', 'price' => 40, 'description' => 'A heat gun for loosening adhesives and paint.', 'image' => 'images/heat gun.jpg'],
            31 => ['name' => 'Caulk Gun', 'price' => 15, 'description' => 'A caulk gun for applying sealants.', 'image' => 'images/caulk gun.jpg'],
            32 => ['name' => 'Air Compressor', 'price' => 150, 'description' => 'An air compressor for various pneumatic tools.', 'image' => 'images/air compressor.jpg'],
            33 => ['name' => 'Jack', 'price' => 60, 'description' => 'A jack for lifting heavy loads.', 'image' => 'images/jack.jpg'],
            34 => ['name' => 'Workbench', 'price' => 200, 'description' => 'A sturdy workbench for your projects.', 'image' => 'images/workbench.jpg'],
            35 => ['name' => 'Welding Machine', 'price' => 300, 'description' => 'A welding machine for metal fabrication.', 'image' => 'images/welding machine.jpg'],
            36 => ['name' => 'Soldering Iron', 'price' => 25, 'description' => 'A soldering iron for electronic repairs.', 'image' => 'images/soldering iron.jpg'],
            37 => ['name' => 'Heat Shrink Tubing', 'price' => 10, 'description' => 'Heat shrink tubing for insulating wires.', 'image' => 'images/heat shrink.jpg'],
            38 => ['name' => 'Cable Ties', 'price' => 5, 'description' => 'Cable ties for securing wires.', 'image' => 'images/cable ties.jpg'],
            39 => ['name' => 'Extension Cord', 'price' => 15, 'description' => 'An extension cord for reaching distant outlets.', 'image' => 'images/extension cord.jpg'],
            40 => ['name' => 'Surge Protector', 'price' => 20, 'description' => 'A surge protector to safeguard electronics.', 'image' => 'images/surge protector.jpg'],
            41 => ['name' => 'Battery Charger', 'price' => 30, 'description' => 'A battery charger for rechargeable batteries.', 'image' => 'images/battery charger.jpg'],
            42 => ['name' => 'Drill Bits Set', 'price' => 22, 'description' => 'A set of drill bits for various materials.', 'image' => 'images/drill bits.jpg'],
            43 => ['name' => 'Saw Blade', 'price' => 18, 'description' => 'A replacement saw blade for cutting wood.', 'image' => 'images/saw blade.jpg'],
            44 => ['name' => 'Chisel Set', 'price' => 20, 'description' => 'A set of chisels for woodworking.', 'image' => 'images/chisel set.jpg'],
            45 => ['name' => 'Miter Saw', 'price' => 250, 'description' => 'A miter saw for precise angled cuts.', 'image' => 'images/miter saw.jpg'],
            46 => ['name' => 'Reciprocating Saw', 'price' => 80, 'description' => 'A reciprocating saw for demolition work.', 'image' => 'images/reciprocating saw.jpg'],
            47 => ['name' => 'Impact Driver', 'price' => 45, 'description' => 'An impact driver for driving screws.', 'image' => 'images/impact driver.jpg'],
            48 => ['name' => 'Orbital Sander', 'price' => 55, 'description' => 'An orbital sander for fine finishing.', 'image' => 'images/orbital sander.jpg'],
            49 => ['name' => 'Air Drill', 'price' => 65, 'description' => 'An air drill for pneumatic operations.', 'image' => 'images/air drill.jpg'],
            50 => ['name' => 'Rotary Tool', 'price' => 35, 'description' => 'A versatile rotary tool for detailed work.', 'image' => 'images/rotary tool.jpg']
        ];
        $insert = $db->prepare("INSERT INTO products (name, price, description, image, stock) VALUES (:name, :price, :description, :image, :stock)");
        foreach ($defaultProducts as $p) {
            $insert->execute([
                ':name'        => $p['name'],
                ':price'       => $p['price'],
                ':description' => $p['description'],
                ':image'       => $p['image'],
                ':stock'       => 50
            ]);
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle Add to Cart submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $prodId = (int) $_POST['product_id'];
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$prodId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product && $product['stock'] > 0) {
        if (isset($_SESSION['cart'][$prodId])) {
            $_SESSION['cart'][$prodId]['quantity']++;
        } else {
            $_SESSION['cart'][$prodId] = $product;
            $_SESSION['cart'][$prodId]['quantity'] = 1;
        }
    }
    header("Location: index.php?page=" . (isset($_GET['page']) ? $_GET['page'] : 1));
    exit();
}

// Pagination: fetch products for the current page
$itemsPerPage = 12;
$stmt = $db->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmt->fetchColumn();
$totalPages = ceil($totalProducts / $itemsPerPage);
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) { $page = 1; }
if ($page > $totalPages) { $page = $totalPages; }
$offset = ($page - 1) * $itemsPerPage;
$stmt = $db->prepare("SELECT * FROM products LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$productsPage = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total cart price for display
$totalCart = 0;
foreach ($_SESSION['cart'] as $item) {
    $totalCart += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ToolHub Marketplace</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; color: #333; }
        .navbar { background-color: #2c3e50; padding: 15px 20px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { color: white; text-decoration: none; margin-left: 10px; padding: 8px 16px; background-color: #e74c3c; border-radius: 4px; transition: background-color 0.3s ease; }
        .navbar a:hover { background-color: #c0392b; }
        .container { width: 90%; max-width: 1200px; margin: 20px auto; }
        .products { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px 0; }
        .product { background-color: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .product:hover { transform: translateY(-5px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .product img { width: 100%; height: auto; border-bottom: 1px solid #ddd; margin-bottom: 10px; }
        .pagination { text-align: center; margin: 20px 0; }
        .pagination a { margin: 0 5px; padding: 8px 12px; text-decoration: none; color: #2c3e50; background-color: #ecf0f1; border-radius: 4px; transition: background-color 0.3s ease; }
        .pagination a.active { background-color: #27ae60; color: white; }
        .pagination a:hover { background-color: #bdc3c7; }
        footer { background-color: #2c3e50; color: white; padding: 15px 20px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?></div>
        <div>
            <a href="cart.php">View Cart (<?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?>) - $<?php echo number_format($totalCart, 2); ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Available Products</h2>
        <div class="products">
            <?php foreach ($productsPage as $product): ?>
                <div class="product">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p><strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                    <p>Stock: <?php echo $product['stock']; ?></p>
                    <form method="post" action="index.php?page=<?php echo $page; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php if($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
        <h3>Total Price: $<?php echo number_format($totalCart, 2); ?></h3>
        <div style="text-align:center; margin:20px;">
            <a href="cart.php" style="padding:10px 20px; background-color:#2980b9; color:white; text-decoration:none; border-radius:4px;">View Cart</a>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 ToolHub Marketplace. All Rights Reserved.</p>
    </footer>
</body>
</html>
