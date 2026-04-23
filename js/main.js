// START NAVBAR TOGGLE
const menuToggle = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");

menuToggle.addEventListener("click", () => {
  navLinks.classList.toggle("active");
  if (navLinks.classList.contains("active")) {
    menuToggle.innerHTML = "✕";
  } else {
    menuToggle.innerHTML = "☰";
  }
});

document.querySelectorAll(".nav-links a").forEach((link) => {
  link.addEventListener("click", () => {
    navLinks.classList.remove("active");
    menuToggle.innerHTML = "☰";
  });
});
// END NAVBAR TOGGLE

// START COUNTER ANIMATION
document.addEventListener("DOMContentLoaded", function () {
  const counter = document.getElementById("counter");
  const plus = document.getElementById("plus");
  const statsHeading = document.querySelector(".stats-container h2");
  let count = 0;
  const target = 40;
  const duration = 2000; //2s
  const increment = target / (duration / 50); //1 langkah 50ms hasilny 1

  const timer = setInterval(() => {
    count += increment;
    if (count >= target) {
      //40 >= 40
      count = target;
      plus.textContent = "+";
      statsHeading.classList.add("bounce");
      clearInterval(timer);
    }
    counter.textContent = Math.floor(count);
  }, 50); //animasi update tiap 50ms
});
// END COUNTER ANIMATION

// START SCROLL REVEAL
const reveals = document.querySelectorAll(".reveal");
const observer = new IntersectionObserver( //mendeteksi apakah elemen masuk ke viewport
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        //elemen sudah masuk ke layar
        entry.target.classList.add("show");
      }
    });
  },
  {
    threshold: 0.15, //animasi aktif saat 15% elemen terlihat
  },
);
reveals.forEach((el) => observer.observe(el));
// END SCROLL REVEAL

// NAVBAR ACTIVE LINK ON SCROLL
const sections = document.querySelectorAll("section");
const navItems = document.querySelectorAll(".nav-links li");

window.addEventListener("scroll", () => {
  let current = "";
  sections.forEach((section) => {
    const sectionTop = section.offsetTop - 120; //section dari atas halaman, karena navbar fixed position
    if (window.scrollY >= sectionTop) {
      //apakah scroll sudah melewati section
      current = section.getAttribute("id");
    }
  });
  navItems.forEach((li) => {
    li.classList.remove("active");

    const link = li.querySelector("a");
    if (link.getAttribute("href") === "#" + current) {
      li.classList.add("active");
    }
  });
});
// END NAVBAR ACTIVE LINK ON SCROLL

// MODAL PRODUCT POPUP
const modal = document.getElementById("productModal");
document.addEventListener("click", (e) => {
  const card = e.target.closest(".product-card");
  if (card) {
    const name = card.getAttribute("data-name");
    const price = card.getAttribute("data-price");
    const desc = card.getAttribute("data-desc");
    const img = card.getAttribute("data-img");
    const badge = card.getAttribute("data-badge");
    const badgeClass = card.getAttribute("data-class");

    document.getElementById("modalTitle").textContent = name;
    document.getElementById("modalPrice").textContent = price;
    document.getElementById("modalDesc").textContent = desc;
    document.getElementById("modalImg").src = img;

    const modalBadge = document.getElementById("modalBadge");
    modalBadge.textContent = badge;
    modalBadge.className = "product-badge " + badgeClass;

    const waText = `Halo, saya ingin memesan produk \n*${name}*`;
    const waLink = `https://wa.me/6285393988929?text=${encodeURIComponent(waText)}`;
    document.getElementById("modalWA").href = waLink;

    modal.classList.add("active");
    document.body.style.overflow = "hidden"; //halaman tidak bsia discroll saat modal dibuka
  }
});

function closeModal() {
  modal.classList.remove("active");
  document.body.style.overflow = "auto"; //halaman bisa discroll saat modal ditutup kembali
}

document.querySelector(".modal-close").addEventListener("click", closeModal);
document.querySelector(".modal-overlay").addEventListener("click", closeModal);
// END PRODUCT POPUP

// START MODAL FORM PRODUCT
document.getElementById("recipe").addEventListener("change", function () {
  const file = this.files[0];
  const fileText = document.getElementById("file-name-text");
  if (file) {
    fileText.textContent = file.name;
  }
});

const contactForm = document.querySelector(".modern-form");

contactForm.addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("name").value;
  const location = document.getElementById("location").value;
  const medicine = document.getElementById("medicine").value;
  const recipeInput = document.getElementById("recipe");
  const recipeName =
    recipeInput.files.length > 0 ? recipeInput.files[0].name : "Tidak ada file";
  const notes = document.getElementById("notes").value || "-";

  document.getElementById("res-name").textContent = name;
  document.getElementById("res-location").textContent = location;
  document.getElementById("res-medicine").textContent = medicine;
  document.getElementById("res-recipe").textContent = recipeName;
  document.getElementById("res-notes").textContent = notes;

  document.getElementById("confirmModal").style.display = "flex";
});

function closeModalForm(modalId) {
  document.getElementById(modalId).style.display = "none";
}

function processPurchase() {
  closeModalForm("confirmModal");
  setTimeout(() => {
    document.getElementById("successModal").style.display = "flex";
    contactForm.reset();
    document.getElementById("file-name-text").textContent =
      "Pilih Foto atau PDF Resep";
  }, 300);
}

window.onclick = function (event) {
  //klik luar modal diclosekan
  const confirmM = document.getElementById("confirmModal");
  const successM = document.getElementById("successModal");
  if (event.target == confirmM) closeModalForm("confirmModal");
  if (event.target == successM) closeModalForm("successModal");
};
// END MODAL FORM PRODUCT

// START BACK TO UP
const backToTopBtn = document.getElementById("backToTop");

window.onscroll = function () {
  if (
    document.body.scrollTop > 300 || //browser lama (kalau scroll udh lebih dari 300px dari atas)
    document.documentElement.scrollTop > 300 //browser baru
  ) {
    backToTopBtn.style.display = "block";
  } else {
    backToTopBtn.style.display = "none";
  }
};
backToTopBtn.addEventListener("click", function () {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});
// END BACK TO UP

// START CATEGORY TOGGLE
function showCategory(id, btn) {
  const contents = document.querySelectorAll(".category-content");
  const buttons = document.querySelectorAll(".category-pill-btn");

  contents.forEach((content) => {
    //semua konten disembunyikan (default)
    content.classList.remove("active");
    content.style.display = "none";
  });

  buttons.forEach((b) => b.classList.remove("active"));

  const targetContent = document.getElementById(id);
  if (targetContent) {
    targetContent.style.display = "block";
    setTimeout(() => {
      targetContent.classList.add("active");
    }, 10);
  }
  btn.classList.add("active");
}
// START CATEGORY TOGGLE
