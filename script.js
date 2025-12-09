

const PRODUCTS = [
  { id: "1", title: "The Silent Patient", author: "Alex Michaelides", price: 25.00, img: "./img/book1.jpg", category: "Thriller", desc: "A gripping psychological thriller." },
  { id: "2", title: "Atomic Habits", author: "James Clear", price: 30.00, img: "./img/book2.jpg", category: "Self-help", desc: "Tiny changes, remarkable results." },
  { id: "3", title: "1984", author: "George Orwell", price: 20.00, img: "./img/book3.jpg", category: "Classic", desc: "Dystopian classic." },
  { id: "4", title: "To Kill a Mockingbird", author: "Harper Lee", price: 22.00, img: "./img/book4.jpg", category: "Classic", desc: "A moving novel of race and conscience." },
  { id: "5", title: "The Alchemist", author: "Paulo Coelho", price: 18.50, img: "./img/book5.jpg", category: "Fiction", desc: "A fable about following your dream." }
];


const $ = sel => document.querySelector(sel);


function getUser() { return JSON.parse(localStorage.getItem('user') || 'null'); }
function setUser(user) { localStorage.setItem('user', JSON.stringify(user)); updateAuthLinks(); }
function logout() { localStorage.removeItem('user'); updateAuthLinks(); alert('Logged out'); }

function updateAuthLinks() {
  const user = getUser();
  const links = document.querySelectorAll('.auth-link');
  links.forEach(el => {
    if (!el) return;
    if (user) {
      el.textContent = 'Logout';
      el.href = '#';
      el.onclick = (e) => { e.preventDefault(); logout(); window.location.href='index.html'; };
    } else {
      el.textContent = 'Login';
      el.href = 'login.html';
      el.onclick = null;
    }
  });
}

function getCart() { return JSON.parse(localStorage.getItem('cart') || '[]'); }
function setCart(cart) { localStorage.setItem('cart', JSON.stringify(cart)); updateCartBadges(); }
function addToCart(product, qty = 1) {
  if (!getUser()) { alert('Please login to add to cart'); window.location.href='login.html'; return; }
  const cart = getCart();
  const found = cart.find(i => i.id === product.id);
  if (found) found.qty += qty;
  else cart.push({ ...product, qty });
  setCart(cart);
  alert(`${product.title} added to cart`);
}
function updateCartBadges() {
  const count = getCart().reduce((s,i)=> s + (i.qty||0),0);
  document.querySelectorAll('.cart-badge').forEach(b => b.textContent = count);


let books = JSON.parse(localStorage.getItem('books')) || [];

function saveBooks() { localStorage.setItem("books", JSON.stringify(books)); }

function renderBooks() {
  const bookList = $('#bookList');
  const tableBody = document.querySelector("#bookTable tbody");
  if (bookList) bookList.innerHTML = "";
  if (tableBody) tableBody.innerHTML = "";

  books.forEach((book, index) => {
    // Admin panel cards
    if (bookList) {
      const div = document.createElement("div");
      div.className = "book-card-admin";
      div.innerHTML = `
        <img src="${book.image}" alt="${book.title}">
        <h4>${book.title}</h4>
        <p>${book.author}</p>
        <p>$${book.price}</p>
        <div class="book-actions">
          <button class="edit-btn" onclick="editBook(${index})">Edit</button>
          <button class="delete-btn" onclick="deleteBook(${index})">Delete</button>
        </div>
      `;
      bookList.appendChild(div);
    }

 
    if (tableBody) {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td><img src="${book.image || './img/book1.jpg'}" alt="book"></td>
        <td>${book.title}</td>
        <td>${book.author}</td>
        <td>$${book.price}</td>
        <td>
          <button class="edit-btn" onclick="editBook(${index})">Edit</button>
          <button class="delete-btn" onclick="deleteBook(${index})">Delete</button>
        </td>
      `;
      tableBody.appendChild(row);
    }
  });

  updateAdminStats(); 
}


function addOrUpdateBook(title, author, price, image, editIndex) {
  if (editIndex === "") books.push({ title, author, price, image });
  else books[editIndex] = { title, author, price, image };
  saveBooks();
  renderBooks();
}

// Delete / Edit
function deleteBook(index) {
  if(confirm("Are you sure you want to delete this book?")) {
    books.splice(index,1);
    saveBooks();
    renderBooks();
  }
}
function editBook(index) {
  const book = books[index];
  $('#bookTitle').value = book.title;
  $('#bookAuthor').value = book.author;
  $('#bookPrice').value = book.price;
  $('#bookImage').value = book.image;
  $('#editIndex').value = index;
  $('#bookForm .btn').textContent = "Update Book";
}

function bindAuthForms() {
  const loginForm = $('#loginForm');
  const regForm = $('#registerForm');
  if (loginForm) {
    loginForm.addEventListener('submit', e => {
      e.preventDefault();
      const email = loginForm.querySelector('input[type="email"]').value.trim();
      const password = loginForm.querySelector('input[type="password"]').value.trim();
      if (email==="admin@example.com" && password==="admin123") {
        localStorage.setItem("isAdmin","true");
        alert("Welcome, Admin!");
        window.location.href="admin.html";
      } else {
        localStorage.setItem("isAdmin","false");
        setUser({ email });
        alert("Login successful!");
        window.location.href="index.html";
      }
    });
  }
  if (regForm) {
    regForm.addEventListener('submit', e => {
      e.preventDefault();
      const name = regForm.querySelector('input[type="text"]').value.trim();
      const email = regForm.querySelector('input[type="email"]').value.trim();
      setUser({ name,email });
      alert("Account created. Logged in as "+email);
      window.location.href="books.html";
    });



const bookForm = $('#bookForm');
if (bookForm) {
  bookForm.addEventListener('submit', e => {
    e.preventDefault();
    addOrUpdateBook(
      $('#bookTitle').value.trim(),
      $('#bookAuthor').value.trim(),
      $('#bookPrice').value.trim(),
      $('#bookImage').value.trim(),
      $('#editIndex').value
    );
    $('#editIndex').value = "";
    $('#bookForm .btn').textContent = "Add Book";
    bookForm.reset();
  });
}

function updateAdminStats() {
  if (!window.location.pathname.includes("admin.html")) return;

  const totalBooks = books.length;
  const totalOrders = JSON.parse(localStorage.getItem('cart') || '[]').length;
  const totalUsers = JSON.parse(localStorage.getItem('users') || '[]').length;

  
  const totalBooksEl = document.getElementById('totalBooks');
  const totalOrdersEl = document.getElementById('totalOrders');
  const totalUsersEl = document.getElementById('totalUsers');

  if(totalBooksEl) totalBooksEl.textContent = totalBooks;
  if(totalOrdersEl) totalOrdersEl.textContent = totalOrders;
  if(totalUsersEl) totalUsersEl.textContent = totalUsers;

  
  const chartCanvas = document.getElementById('salesChart');
  if(chartCanvas) {
    if(window.salesChart) window.salesChart.destroy();  
    window.salesChart = new Chart(chartCanvas, {
      type: 'bar',
      data: {
        labels: books.map(b => b.title),
        datasets: [{
          label: 'Units Sold',
          data: books.map(b => Math.floor(Math.random()*50)), 
          backgroundColor: 'rgba(63, 81, 181, 0.7)',
          borderColor: 'rgba(63, 81, 181, 1)',
          borderWidth: 1
        }]
      },
      options: { responsive:true, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });
  }
}


document.addEventListener('DOMContentLoaded', () => {
  
  bindAuthForms();
  updateAuthLinks();

 
  updateCartBadges();

  
  renderBooks();

  
  if (window.location.pathname.includes("admin.html")) {
    if (localStorage.getItem("isAdmin")!=="true") {
      alert("Access denied! Admins only.");
      window.location.href="login.html";
    }
    $('#logoutBtn')?.addEventListener('click', () => {
      localStorage.removeItem("isAdmin");
      alert("Logged out!");
      window.location.href="login.html";
    });
  }
});

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector(this.getAttribute('href'))
      .scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});


const faders = document.querySelectorAll('.feature, .book-card');
const appearOptions = { threshold: 0.2, rootMargin: "0px 0px -50px 0px" };
const appearOnScroll = new IntersectionObserver((entries, appearOnScroll) => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    entry.target.classList.add('show');
    appearOnScroll.unobserve(entry.target);
  });
}, appearOptions);

faders.forEach(fader => {
  fader.classList.add('fade-in'); 
  appearOnScroll.observe(fader);
});

window.addEventListener('load', () => {
  const hero = document.querySelector('.hero-left');
  if(hero) hero.classList.add('show');
});
