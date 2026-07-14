// START NAVBAR TOGGLE
const menuToggle = document.querySelector(".menu-toggle");
const navLinks = document.querySelector(".nav-links");

if (menuToggle && navLinks) {
  menuToggle.addEventListener("click", () => {
    navLinks.classList.toggle("active");
    menuToggle.innerHTML = navLinks.classList.contains("active") ? "✕" : "☰";
  });

  document.querySelectorAll(".nav-links a").forEach((link) => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("active");
      menuToggle.innerHTML = "☰";
    });
  });
}
// END NAVBAR TOGGLE

// START COUNTER ANIMATION
document.addEventListener("DOMContentLoaded", function () {
  const counter = document.getElementById("counter");
  const plus = document.getElementById("plus");
  const statsHeading = document.querySelector(".stats-container h2");

  if (!counter || !plus || !statsHeading) return;

  let count = 0;
  const target = 40;
  const duration = 2000; //2s

  const timer = setInterval(() => {
    count++;
    if (count >= target) {
      //40 >= 40
      count = target;
      plus.textContent = "+";
      statsHeading.classList.add("bounce");
      clearInterval(timer);
    }
    counter.textContent = count;
  }, 50); //animasi update tiap 50ms
});
// END COUNTER ANIMATION

// START SCROLL REVEAL
const reveals = document.querySelectorAll(".reveal");

if (reveals.length) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("show");
        }
      });
    },
    { threshold: 0.15 },
  );

  reveals.forEach((el) => observer.observe(el));
}
// END SCROLL REVEAL

// NAVBAR ACTIVE LINK ON SCROLL
const sections = document.querySelectorAll("section");
const navItems = document.querySelectorAll(".nav-links li");

window.addEventListener("scroll", () => {
  const isIndexPage = document.getElementById("about");
  if (!isIndexPage) return;

  let current = "";
  sections.forEach((section) => {
    const sectionTop = section.offsetTop - 150;
    if (window.scrollY >= sectionTop) {
      current = section.getAttribute("id");
    }
  });

  if (current === "why") {
    current = "about";
  }

  if (current === "stats") {
    current = "home";
  }

  if (current !== "") {
    navItems.forEach((li) => {
      li.classList.remove("active");
      const link = li.querySelector("a");
      if (link.getAttribute("href").includes("#" + current)) {
        li.classList.add("active");
      }
    });
  }
});
// END NAVBAR ACTIVE LINK ON SCROLL

// MODAL PRODUCT POPUP
const modal = document.getElementById("productModal");
if (modal) {
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

      const waText = `Halo, saya ingin memesan produk *${name}*`;
      const waLink = `https://wa.me/6285393988929?text=${encodeURIComponent(waText)}`;
      document.getElementById("modalWA").href = waLink;

      modal.classList.add("active");
      document.body.style.overflow = "hidden"; //halaman tidak bisa discroll saat modal dibuka
    }
  });
}

if (modal) {
  function closeModal() {
    modal.classList.remove("active");
    document.body.style.overflow = "auto";
  }

  const modalClose = document.querySelector(".modal-close");
  const modalOverlay = document.querySelector(".modal-overlay");

  if (modalClose) {
    modalClose.addEventListener("click", closeModal);
  }

  if (modalOverlay) {
    modalOverlay.addEventListener("click", closeModal);
  }
}
// END PRODUCT POPUP

// START MODAL FORM PRODUCT
const recipe = document.getElementById("recipe");
if (recipe) {
  recipe.addEventListener("change", function () {
    const file = this.files[0];
    const formGroup = this.closest(".form-group");
    const fileText = document.getElementById("file-name-text");

    if (file) {
      const fileSize = file.size / (1024 * 1024);
      if (fileSize > 5) {
        formGroup.classList.add("error");
        fileText.textContent = "Ukuran file terlalu besar (Maksimal 5 MB)!";
        this.value = "";
      } else {
        formGroup.classList.remove("error");
        fileText.textContent = file.name;
      }
    } else {
      formGroup.classList.remove("error");
      fileText.textContent = "Pilih file resep...";
    }
  });
}

// START BACK TO UP
const backToTopBtn = document.getElementById("backToTop");
if (backToTopBtn) {
  window.addEventListener("scroll", function () {
    backToTopBtn.style.display =
      document.documentElement.scrollTop > 300 ? "block" : "none";
  });

  backToTopBtn.addEventListener("click", function () {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
}
// END BACK TO UP

// START CATEGORY TOGGLE
function showCategory(id, btn) {
  const contents = document.querySelectorAll(".category-content");
  const buttons = document.querySelectorAll(".category-pill-btn");

  contents.forEach((content) => {
    //semua konten disembunyikan
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
// END CATEGORY TOGGLE

//START ADD NEW PRODUCT
const btnAddProduct = document.getElementById("btn-add-product");
const produkContainer = document.getElementById("produk-container");

if (btnAddProduct && produkContainer) {
  btnAddProduct.addEventListener("click", function () {
    const firstRow = document.querySelector(".produk-row");
    if (!firstRow) return;

    const newRow = firstRow.cloneNode(true);

    newRow.querySelector("select").value = "";
    newRow.querySelector("input").value = "";

    produkContainer.appendChild(newRow);
    updateSelectOptions();
  });

  produkContainer.addEventListener("change", function (e) {
    if (e.target.classList.contains("select-obat")) {
      updateSelectOptions();
    }
  });

  updateSelectOptions();
}

function hapusBaris(button) {
  const rows = document.querySelectorAll(".produk-row");
  if (rows.length > 1) {
    button.closest(".produk-row").remove();
    updateSelectOptions();
  } else {
    const row = button.closest(".produk-row");
    row.querySelector("select").value = "";
    row.querySelector("input").value = "";
    updateSelectOptions();
  }
}

function updateSelectOptions() {
  const allSelects = document.querySelectorAll(".select-obat");

  const selectedValues = Array.from(allSelects)
    .map((select) => select.value)
    .filter((val) => val !== "");

  allSelects.forEach((currentSelect) => {
    const options = currentSelect.querySelectorAll("option");

    options.forEach((option) => {
      if (option.value === "") return;
      if (
        selectedValues.includes(option.value) &&
        currentSelect.value !== option.value
      ) {
        option.disabled = true;
        option.style.color = "#ccc";
      } else {
        option.disabled = false;
        option.style.color = "";
      }
    });
  });
}

updateSelectOptions();
//END ADD NEW PRODUCT

//START PREVIEW PHOTO
function previewImage() {
  const image = document.querySelector("#photo");
  const imgPreview = document.querySelector("#img-preview");
  if (!image || !imgPreview) return;
  if (image.files && image.files[0]) {
    imgPreview.classList.remove("d-none");

    const oFReader = new FileReader();
    oFReader.readAsDataURL(image.files[0]);

    oFReader.onload = function (oFREvent) {
      imgPreview.src = oFREvent.target.result;
    };
  } else {
    imgPreview.src = "";
    imgPreview.classList.add("d-none");
  }
}
//END PREVIEW PHOTO

//START HIDE PASSWORD
const togglePassword = document.getElementById("togglePassword");

if (togglePassword) {
  togglePassword.addEventListener("click", function () {
    const passwordInput = document.getElementById("password");
    const toggleIcon = document.getElementById("toggleIcon");

    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      toggleIcon.classList.remove("bi-eye");
      toggleIcon.classList.add("bi-eye-slash");
    } else {
      passwordInput.type = "password";
      toggleIcon.classList.remove("bi-eye-slash");
      toggleIcon.classList.add("bi-eye");
    }
  });
}
//END HIDE PASSWORD
