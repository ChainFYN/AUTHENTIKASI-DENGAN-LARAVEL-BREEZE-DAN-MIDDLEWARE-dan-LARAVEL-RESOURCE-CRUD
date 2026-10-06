let products = [];
let cart = [];

const searchInput = document.getElementById("searchProduct");
const productResults = document.getElementById("productResults");

if (searchInput) {
    let searchTimer = null;
    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(searchProduct, 250);
    });

    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const first = productResults.querySelector(".product-item[data-id]");
            if (first) {
                addToCart(parseInt(first.getAttribute("data-id")));
            }
        }
    });
}

async function searchProduct() {
    const keyword = searchInput.value.trim();
    if (!keyword) {
        productResults.style.display = "none";
        return;
    }

    try {
        const res = await fetch('/api/products/search?q=' + encodeURIComponent(keyword));
        products = await res.json();
    } catch (e) {
        console.error('Gagal memuat produk:', e);
        products = [];
    }

    if (products.length === 0) {
        productResults.innerHTML = '<div class="product-item">Barang tidak ditemukan</div>';
    } else {
        productResults.innerHTML = products.map(p => `
            <div class="product-item" data-id="${p.id}" onclick="addToCart(${p.id})">
                <div>${p.code || ''}</div>
                <div class="product-name">${p.name}</div>
                <div class="product-price">${formatRupiah(p.price)}</div>
                <div style="font-size:11px;color:#999">Stok: ${p.stock}</div>
            </div>
        `).join("");
    }
    productResults.style.display = "block";
}

function formatRupiah(value) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(value);
}

if (searchInput) {
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-input")) {
            productResults.style.display = "none";
        }
    });
}

function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existing = cart.find(item => item.id === productId);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id: product.id,
            code: product.code,
            name: product.name,
            price: parseFloat(product.price),
            qty: 1
        });
    }

    if (searchInput) {
        searchInput.value = "";
        productResults.style.display = "none";
    }
    renderCart();
}

function renderCart() {
    const body = document.getElementById("cartBody");
    if (!body) return;

    if (cart.length === 0) {
        body.innerHTML = '<tr><td colspan="6" class="empty-cart">Keranjang masih kosong.</td></tr>';
        calculateTotal();
        return;
    }

    body.innerHTML = cart.map((item, index) => `
        <tr>
            <td>${index + 1}</td>
            <td><strong>${item.name}</strong><div style="font-size:11px; color:#666">${item.code}</div></td>
            <td>${formatRupiah(item.price)}</td>
            <td><div class="qty-control"><button onclick="changeQty(${item.id}, -1)">-</button><input type="number" value="${item.qty}" onchange="updateQty(${item.id}, this.value)"><button onclick="changeQty(${item.id}, 1)">+</button></div></td>
            <td class="text-right"><strong>${formatRupiah(item.price * item.qty)}</strong></td>
            <td><button class="remove-btn" onclick="removeItem(${item.id})">x</button></td>
        </tr>
    `).join("");
    calculateTotal();
}

function changeQty(id, amount) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += amount;
    if (item.qty <= 0) removeItem(id);
    else renderCart();
}

function updateQty(id, qty) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty = Math.max(1, parseInt(qty) || 1);
    renderCart();
}

function removeItem(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function calculateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const discountPercent = parseFloat(document.getElementById("discountPercent")?.value) || 0;
    const manualDiscount = parseFloat(document.getElementById("discountAmount")?.value) || 0;
    const tax = parseFloat(document.getElementById("tax")?.value) || 0;
    const otherFee = parseFloat(document.getElementById("otherFee")?.value) || 0;

    const grandTotal = Math.max(0, subtotal - (subtotal * discountPercent / 100) - manualDiscount + tax + otherFee);

    const totalQtyEl = document.getElementById("totalQty");
    const subtotalEl = document.getElementById("subtotal");
    const grandTotalEl = document.getElementById("grandTotal");

    if (totalQtyEl) totalQtyEl.innerText = cart.reduce((sum, item) => sum + item.qty, 0);
    if (subtotalEl) subtotalEl.innerText = formatRupiah(subtotal);
    if (grandTotalEl) grandTotalEl.innerText = formatRupiah(grandTotal);

    calculateChange();
}

function calculateChange() {
    const grandTotalEl = document.getElementById("grandTotal");
    const paymentEl = document.getElementById("payment");
    const changeBox = document.getElementById("changeBox");
    const changeEl = document.getElementById("change");

    if (!grandTotalEl || !paymentEl || !changeBox || !changeEl) return;

    const total = parseFloat(grandTotalEl.innerText.replace(/[^0-9]/g, "")) || 0;
    const payment = parseFloat(paymentEl.value) || 0;
    const change = payment - total;

    if (change >= 0) {
        changeBox.classList.remove("short-payment");
        changeEl.innerText = formatRupiah(change);
    } else {
        changeBox.classList.add("short-payment");
        changeEl.innerText = formatRupiah(Math.abs(change));
    }
}

function selectPayment(btn) {
    document.querySelectorAll(".payment-method button").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
}

function holdTransaction() {
    alert("Transaksi berhasil ditahan.");
}

function cancelTransaction() {
    if (!confirm("Batalkan transaksi?")) return;
    cart = [];
    document.getElementById("payment").value = "";
    renderCart();
}

async function processPayment() {
    if (cart.length === 0) { alert("Keranjang masih kosong."); return; }

    const grandTotalEl = document.getElementById("grandTotal");
    const paymentEl = document.getElementById("payment");
    const total = parseFloat(grandTotalEl.innerText.replace(/[^0-9]/g, "")) || 0;
    const payment = parseFloat(paymentEl.value) || 0;

    if (payment < total) { alert("Uang pembayaran masih kurang."); return; }

    const paymentMethod = document.querySelector(".payment-method button.active")?.innerText || "Tunai";

    try {
        const res = await fetch("/transactions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
            },
            body: JSON.stringify({
                items: cart.map(i => ({ product_id: i.id, qty: i.qty, discount: 0 })),
                subtotal: cart.reduce((s, i) => s + i.price * i.qty, 0),
                discount: parseFloat(document.getElementById("discountPercent")?.value) || 0,
                tax: parseFloat(document.getElementById("tax")?.value) || 0,
                other_fee: parseFloat(document.getElementById("otherFee")?.value) || 0,
                grand_total: total,
                paid_amount: payment,
                change_amount: payment - total,
                payment_method: paymentMethod,
            }),
        });
        const result = await res.json();

        if (result.success) {
            alert(
                "Pembayaran berhasil!\n\n" +
                "No. Transaksi : " + result.transaction_number + "\n" +
                "Total         : " + formatRupiah(result.grand_total) + "\n" +
                "Bayar         : " + formatRupiah(payment) + "\n" +
                "Kembali       : " + formatRupiah(result.change_amount)
            );
            cart = [];
            paymentEl.value = "";
            renderCart();
        } else {
            alert("Gagal: " + result.message);
        }
    } catch (e) {
        console.error('Error:', e);
        alert("Terjadi kesalahan saat menyimpan transaksi.");
    }
}

document.addEventListener("keydown", (e) => {
    if (e.key === "F2" && searchInput) searchInput.focus();
    if (e.key === "Escape") cancelTransaction();
});

document.addEventListener("DOMContentLoaded", function() {
    renderCart();
});