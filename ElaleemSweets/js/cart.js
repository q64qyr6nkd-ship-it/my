// جلب عناصر السلة
const cartCount = document.getElementById("cart-count");
const cartItemsContainer = document.getElementById("cart-items");

let cart = JSON.parse(localStorage.getItem("cart")) || [];

// تحديث عداد السلة
function updateCartCount() {
  const totalCount = cart.reduce((acc, item) => acc + item.quantity, 0);
  if(cartCount) cartCount.textContent = totalCount;
}

// عرض محتويات السلة
function renderCart() {
  if(!cartItemsContainer) return;
  cartItemsContainer.innerHTML = "";
  let totalPrice = 0;

  cart.forEach((item, index) => {
    totalPrice += item.price * item.quantity;

    const div = document.createElement("div");
    div.className = "cart-item";
    div.innerHTML = `
      <img src="${item.image}" alt="${item.name}" />
      <h3>${item.name}</h3>
      <p>السعر: ${item.price} د.ل</p>
      <p>الكمية: ${item.quantity}</p>
      <button class="remove-item" data-index="${index}">حذف</button>
    `;
    cartItemsContainer.appendChild(div);
  });

  const totalPriceElem = document.getElementById("total-price");
  if(totalPriceElem) totalPriceElem.textContent = totalPrice;
}

// إزالة منتج
document.addEventListener("click", function(e){
  if(e.target.classList.contains("remove-item")){
    const idx = e.target.getAttribute("data-index");
    cart.splice(idx,1);
    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();
    renderCart();
  }
});

// أزرار إضافة للسلة في الصفحات الأخرى
document.querySelectorAll(".add-to-cart").forEach(btn => {
  const card = btn.closest(".product-card");
  const quantityElem = card.querySelector(".quantity span");

  btn.addEventListener("click", () => {
    const product = {
      name: card.querySelector("h3").textContent,
      price: parseFloat(card.querySelector(".price").textContent),
      quantity: parseInt(quantityElem.textContent),
      image: card.querySelector("img").src
    };

    const existing = cart.find(item => item.name === product.name);
    if(existing){
      existing.quantity += product.quantity;
    } else {
      cart.push(product);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
    updateCartCount();

    btn.textContent = `✅ تمت الإضافة (${product.quantity})`;
    btn.disabled = true;
    setTimeout(() => {
      btn.textContent = "أضف للسلة";
      btn.disabled = false;
      quantityElem.textContent = "1";
    }, 1200);
  });
});

// عرض السلة عند تحميل الصفحة
renderCart();
updateCartCount();