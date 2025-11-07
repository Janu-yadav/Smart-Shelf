<?php
include 'db.php';

// Handle product addition
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir);
    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image_name;
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, is_active) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("sdss", $name, $price, $desc, $image_name);
    $stmt->execute();
    $stmt->close();

    header("Location: add_product.php?msg=Product+added+successfully");
    exit;
}

// Fetch products
$activeProducts = $conn->query("SELECT * FROM products WHERE is_active = 1");
$removedProducts = $conn->query("SELECT * FROM products WHERE is_active = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SmartShelf - Manage Products</title>
<style>
body {
  font-family: "Poppins", sans-serif;
  background: radial-gradient(circle at top left, #0f2027, #203a43, #2c5364);
  color: #f1f1f1;
  margin: 0;
  padding: 0;
}
header {
  background: #000;
  color: #fff;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
}
.header-buttons a {
  color: white;
  text-decoration: none;
  background: linear-gradient(90deg, #00c6ff, #0072ff);
  padding: 8px 15px;
  border-radius: 8px;
  margin-left: 10px;
  transition: all 0.3s;
}
.header-buttons a:hover {
  background: linear-gradient(90deg, #0072ff, #00c6ff);
  box-shadow: 0 0 10px #00c6ff;
}
.container { padding: 30px; }

.toggle-btn {
  display: block;
  width: 60%;
  margin: 0 auto 20px auto;
  background: linear-gradient(90deg, #00c6ff, #0072ff);
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 16px;
  transition: all 0.3s;
}
.toggle-btn:hover {
  box-shadow: 0 0 15px #00c6ff;
}

.add-form {
  background: rgba(255,255,255,0.08);
  padding: 25px;
  border-radius: 12px;
  margin-bottom: 30px;
  box-shadow: 0 0 20px rgba(0,255,255,0.15);
  width: 60%;
  margin: 20px auto;
  display: none;
}
.add-form h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #00c6ff;
}
.add-form input, .add-form textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: none;
  border-radius: 8px;
  outline: none;
  font-size: 15px;
}
.add-form input[type="file"] {
  background: none;
  color: #ccc;
}
.add-form button {
  background: linear-gradient(90deg, #00ff99, #00b894);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  width: 100%;
  transition: all 0.3s;
}
.add-form button:hover {
  box-shadow: 0 0 10px #00ff99;
}

.search-section {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin-bottom: 25px;
}
.search-section input, .search-section select {
  padding: 10px;
  border-radius: 8px;
  border: none;
  font-size: 16px;
  outline: none;
}
.search-section input {
  width: 40%;
}
.search-section select {
  background: #00c6ff;
  color: white;
  cursor: pointer;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  justify-content: center;
  gap: 25px;
}
.card {
  background: rgba(255,255,255,0.08);
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 0 15px rgba(0,255,255,0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  max-width: 270px;
  margin: 0 auto;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 0 25px rgba(0,255,255,0.3);
}
.card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
}
.card-content { padding: 15px; }
.card h3 { margin: 0 0 8px; color: #fff; }
.price { color: #00c6ff; font-weight: bold; margin-bottom: 8px; }
.rental { color: #ccc; font-size: 14px; }
.actions { margin-top: 12px; display: flex; gap: 5px; }
.actions button {
  flex: 1;
  background: linear-gradient(90deg, #00ff99, #00b894);
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s;
}
.actions button:hover { box-shadow: 0 0 10px #00ff99; }
.restore-btn {
  background: linear-gradient(90deg, #ff8a00, #e52e71);
}
</style>
</head>
<body>

<header>
  <h2>SmartShelf - Manage Products</h2>
  <div class="header-buttons">
    <a href="dashboard.php">Dashboard</a>
  </div>
</header>

<div class="container">

  <button class="toggle-btn" id="toggleForm">➕ Add New Product</button>

  <form class="add-form" id="addForm" action="" method="POST" enctype="multipart/form-data">
    <h2>Add Product</h2>
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" name="price" step="0.01" placeholder="Price (₹)" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_product">Add Product</button>
  </form>

  <div class="search-section">
    <input type="text" id="searchInput" placeholder="Search products...">
    <select id="viewSelect">
      <option value="active">Active Products</option>
      <option value="removed">Removed Products</option>
    </select>
  </div>

  <div id="activeSection" class="grid">
    <?php while ($row = $activeProducts->fetch_assoc()): ?>
      <div class="card">
        <img src="uploads/<?php echo $row['image']; ?>" alt="Product Image">
        <div class="card-content">
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <p class="price">₹<?php echo number_format($row['price'], 2); ?></p>
          <p class="rental"><?php echo htmlspecialchars($row['description']); ?></p>
          <div class="actions">
            <form action="remove_product.php" method="get">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit">Remove</button>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>

  <div id="removedSection" class="grid" style="display: none;">
    <?php while ($row = $removedProducts->fetch_assoc()): ?>
      <div class="card">
        <img src="uploads/<?php echo $row['image']; ?>" alt="Product Image">
        <div class="card-content">
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <p class="price">₹<?php echo number_format($row['price'], 2); ?></p>
          <p class="rental"><?php echo htmlspecialchars($row['description']); ?></p>
          <div class="actions">
            <form action="restore_product.php" method="get">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit" class="restore-btn">Restore</button>
            </form>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<script>
const toggleFormBtn = document.getElementById("toggleForm");
const addForm = document.getElementById("addForm");
toggleFormBtn.addEventListener("click", () => {
  const isHidden = addForm.style.display === "none" || addForm.style.display === "";
  addForm.style.display = isHidden ? "block" : "none";
  toggleFormBtn.textContent = isHidden ? "✖ Hide Add Product" : "➕ Add New Product";
});

const searchInput = document.getElementById("searchInput");
const viewSelect = document.getElementById("viewSelect");
const activeSection = document.getElementById("activeSection");
const removedSection = document.getElementById("removedSection");

viewSelect.addEventListener("change", () => {
  if (viewSelect.value === "active") {
    activeSection.style.display = "grid";
    removedSection.style.display = "none";
  } else {
    activeSection.style.display = "none";
    removedSection.style.display = "grid";
  }
  searchInput.value = "";
  filterCards();
});

searchInput.addEventListener("keyup", filterCards);

function filterCards() {
  const searchValue = searchInput.value.toLowerCase();
  const visibleSection = viewSelect.value === "active" ? activeSection : removedSection;
  const cards = visibleSection.querySelectorAll(".card");
  cards.forEach(card => {
    const title = card.querySelector("h3").innerText.toLowerCase();
    card.style.display = title.includes(searchValue) ? "block" : "none";
  });
}
</script>

</body>
</html>
