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
  const formGroup = this.closest(".form-group");
  const fileText = document.getElementById("file-name-text");
  if (file) {
    const fileSize = file.size / (1024 * 1024); //ke mb

    if (fileSize > 10) {
      formGroup.classList.add("error");
      this.value = "";
    } else {
      formGroup.classList.remove("error");
      fileText.textContent = file.name;
    }
  } else {
    formGroup.classList.remove("error");
  }
});

// const contactForm = document.querySelector(".modern-form");
// contactForm.addEventListener("submit", function (e) {
//   e.preventDefault();

//   const name = document.getElementById("name");
//   const location = document.getElementById("location");
//   const medicine = document.getElementById("medicine");
//   const recipe = document.getElementById("recipe");
//   const notes = document.getElementById("notes");

//   const nameValue = name.value;
//   const locationValue = location.value;
//   const medicineValue = medicine.value || "-";
//   const recipeName =
//     recipe.files.length > 0 ? recipe.files[0].name : "Tidak ada file";
//   const notesValue = notes.value || "-";

//   let isValid = true;

//   if (nameValue === "") {
//     name.parentElement.classList.add("error");
//     isValid = false;
//   } else {
//     name.parentElement.classList.remove("error");
//   }

//   if (locationValue === "") {
//     location.parentElement.classList.add("error");
//     isValid = false;
//   } else {
//     location.parentElement.classList.remove("error");
//   }

//   if (isValid) {
//     recipe.closest(".form-group").classList.remove("error");
//     document.getElementById("res-name").textContent = nameValue;
//     document.getElementById("res-location").textContent = locationValue;
//     document.getElementById("res-medicine").textContent = medicineValue;
//     document.getElementById("res-recipe").textContent = recipeName;
//     document.getElementById("res-notes").textContent = notesValue;
//     document.getElementById("confirmModal").style.display = "flex";
//   }
// });

// const inputs = document.querySelectorAll(
//   ".modern-form input, .modern-form textarea",
// );

// inputs.forEach((input) => {
//   input.addEventListener("input", function () {
//     const formGroup = this.closest(".form-group");

//     if (formGroup.classList.contains("error")) {
//       formGroup.classList.remove("error");
//     }
//   });
// });

// function closeModalForm(modalId) {
//   document.getElementById(modalId).style.display = "none";
// }

// function processPurchase() {
//   const nameValue = document.getElementById("res-name").textContent;
//   const locationValue = document.getElementById("res-location").textContent;
//   const medicineValue = document.getElementById("res-medicine").textContent;
//   const notesValue = document.getElementById("res-notes").textContent;

//   let pesan = `*Pesanan Obat Baru*\n\n`;
//   pesan += `*Nama:* ${nameValue}\n`;
//   pesan += `*Lokasi:* ${locationValue}\n`;
//   pesan += `*Obat:* ${medicineValue}\n`;
//   pesan += `*Catatan:* ${notesValue}\n\n`;
//   pesan += `_Mohon segera diproses, terima kasih._`;

//   const pesanEncoded = encodeURIComponent(pesan);
//   const urlWa = `https://wa.me/6285393988929?text=${pesanEncoded}`;
//   window.open(urlWa, "_blank");
//   closeModalForm("confirmModal");

//   setTimeout(() => {
//     document.getElementById("successModal").style.display = "flex";
//     contactForm.reset();
//     document.getElementById("file-name-text").textContent =
//       "Pilih Foto atau PDF Resep";
//   }, 300);
// }

// window.onclick = function (event) {
//   //klik luar modal diclosekan
//   const confirmM = document.getElementById("confirmModal");
//   const successM = document.getElementById("successModal");
//   if (event.target == confirmM) closeModalForm("confirmModal");
//   if (event.target == successM) closeModalForm("successModal");
// };
// END MODAL FORM PRODUCT

// START BACK TO UP
const backToTopBtn = document.getElementById("backToTop");

window.onscroll = function () {
  if (
    document.documentElement.scrollTop > 300 //kalau scroll udh lebih dari 300px dari atas
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
document
  .getElementById("btn-add-product")
  .addEventListener("click", function () {
    const container = document.getElementById("produk-container");
    const firstRow = document.querySelector(".produk-row");

    const newRow = firstRow.cloneNode(true);

    newRow.querySelector("select").value = "";
    newRow.querySelector("input").value = "";

    container.appendChild(newRow);
  });

function hapusBaris(button) {
  const rows = document.querySelectorAll(".produk-row");
  if (rows.length > 1) {
    button.closest(".produk-row").remove();
  } else {
    const row = button.closest(".produk-row");
    row.querySelector("select").value = "";
    row.querySelector("input").value = "";
  }
}
//END ADD NEW PRODUCT
//START PREVIEW PHOTO
function previewImage() {
  const image = document.querySelector("#photo");
  const imgPreview = document.querySelector("#img-preview");

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
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});
//END HIDE PASSWORD