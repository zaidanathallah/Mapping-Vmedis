document.addEventListener("DOMContentLoaded", function () {

  // Pastikan Fungsi Dimuat di Awal
  document.addEventListener('DOMContentLoaded', function () {
      if (window.location.pathname === '/provinsi.html') {
          loadProvinsiList();
      }
  });
  // Function to calculate the similarity percentage using similar_text algorithm

  document.getElementById("select-30-btn-provinsi").onclick = function () {
      document.getElementById("selectModal").style.display = "block";
  };

  function confirmSelectProvinsi() {
      const checkboxes = document.querySelectorAll(".select-checkbox");
      let selectedCount = 0; // Track the number of selected checkboxes

      checkboxes.forEach((checkbox, index) => {
          console.log(
              `Checkbox ${index}: Disabled=${checkbox.disabled}, Checked=${checkbox.checked}`
          ); // Log each checkbox's state
          if (!checkbox.disabled && selectedCount < 30) {
              // Only select if not disabled and count is under 30
              checkbox.checked = true; // Check the box if it's not disabled
              selectedCount++; // Increase the count for each selected checkbox
          }
      });

      console.log("Total selected checkboxes:", selectedCount); // Check how many were selected
      closeModalProvinsi("selectModal");
  }

  // Delete selected data with confirmation
  document.getElementById("delete-selected-btn-provinsi").onclick =
      function () {
          document.getElementById("deleteModal").style.display = "block";
      };

  let currentPage = 1; // Halaman saat ini
  const itemsPerPage = 30; // Jumlah item per halaman
  const pagesToShow = 5; // Jumlah angka halaman yang ditampilkan sekaligus
  let totalPages = 0; // Total halaman yang akan dihitung setelah data dimuat
  let searchParams = {}; // Simpan pencarian terakhir

  // Fungsi untuk memuat data provinsi
  function loadProvinsiList(searchParams = {}) {
      console.log("Memuat data dengan parameter pencarian:", searchParams);
      const tableBody = document.getElementById("obat-list-provinsi");

      // Tambahkan parameter pencarian ke URL
      const queryString = new URLSearchParams(searchParams).toString();
      console.log(`Query string yang dikirim: get_provinsi.php?${queryString}`);

      fetch(`get_provinsi.php?${queryString}`)
          .then((response) => response.json())
          .then((data) => {
              console.log("Data yang diterima:", data);
              // Kosongkan tabel sebelum mengisi
              tableBody.innerHTML = "";

              const totalData = data.length; // Hitung total data

              // Jika tidak ada data, tampilkan pesan "No results found"
              if (totalData === 0) {
                  const noDataRow = `<tr>
                              <td colspan="4" class="text-center">No results found</td>
                          </tr>`;
                  tableBody.innerHTML = noDataRow;

                  // Update informasi pagination dan sembunyikan pagination jika tidak ada data
                  updatePaginationInfoProvinsi(totalData);
                  document.getElementById("pagination-controls-provinsi").innerHTML =
                      "";
                  return; // Berhenti di sini jika tidak ada data
              }

              totalPages = Math.ceil(totalData / itemsPerPage); // Hitung total halaman

              // Tentukan indeks awal dan akhir data untuk halaman saat ini
              const startIndex = (currentPage - 1) * itemsPerPage;
              const endIndex = startIndex + itemsPerPage;

              // Ambil data yang sesuai dengan halaman saat ini
              const pageData = data.slice(startIndex, endIndex);

              // Tampilkan data pada tabel
              pageData.forEach((Provinsi) => {
                  // Ubah nilai provinsiaktif menjadi "Aktif" atau "Non Aktif"
                  const provinsiAktifText =
                      Provinsi.provinsiaktif === "1" ? "Aktif" : "Non Aktif";

                  // Tentukan apakah checkbox harus dinonaktifkan
                  const isDisabled =
                      !Provinsi.idprovinsi_satusehat ||
                          Provinsi.idprovinsi_satusehat.trim() === ""
                          ? ""
                          : "disabled";

                  const row = `<tr>
                <td class="checkbox-column-provinsi">
                    <input type="checkbox" class="select-checkbox" ${isDisabled}>
                    <button class="delete-icon-provinsi" onclick="delete_idprovinsi_Satusehat('${Provinsi.provinsi}')">🗑️</button>
                </td>
                <td>${Provinsi.provinsi}</td>
                <td>${provinsiAktifText}</td>
                <td>${Provinsi.idprovinsi_satusehat}</td>
            </tr>`;

                  tableBody.innerHTML += row;
              });

              // Update kontrol pagination
              updatePaginationControlsProvinsi();
              // Update informasi pagination
              updatePaginationInfoProvinsi(totalData);
          })
          .catch((error) => console.error("Error:", error));
  }



  // Fungsi Global
  window.delete_idprovinsi_Satusehat = function (provinsi) {
      if (confirm('Apakah Anda yakin ingin menghapus ID Provinsi Satu Sehat?')) {
          fetch(`delete_idprovinsi_Satusehat.php?provinsi=${provinsi}`, {
              method: 'GET'
          })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      alert('ID Provinsi Satu Sehat berhasil dihapus');
                      loadProvinsiList(searchParams); // Muat ulang data setelah penghapusan
                  } else {
                      alert('Gagal menghapus ID Provinsi Satu Sehat');
                  }
              })
              .catch(error => console.error('Error:', error));
      }
  };

  // Fungsi untuk mengupdate kontrol pagination
  function updatePaginationControlsProvinsi() {
      const paginationContainer = document.getElementById(
          "pagination-controls-provinsi"
      );
      paginationContainer.innerHTML = ""; // Kosongkan kontrol pagination sebelumnya

      const startPage = Math.max(1, currentPage - Math.floor(pagesToShow / 2));
      const endPage = Math.min(totalPages, startPage + pagesToShow - 1);

      // Tambahkan tombol "Previous"
      const prevButton = document.createElement("button");
      prevButton.innerHTML = "&laquo;"; // Karakter panah ke kiri
      prevButton.classList.add("page-button");
      prevButton.disabled = currentPage === 1; // Nonaktifkan jika di halaman pertama

      prevButton.addEventListener("click", function () {
          if (currentPage > 1) {
              currentPage--; // Kurangi halaman saat ini
              loadProvinsiList(searchParams); // Muat data untuk halaman sebelumnya dengan parameter pencarian
          }
      });

      paginationContainer.appendChild(prevButton);

      // Buat tombol untuk setiap halaman yang ditampilkan
      for (let i = startPage; i <= endPage; i++) {
          const pageButton = document.createElement("button");
          pageButton.innerText = i;
          pageButton.classList.add("page-button");

          // Tambahkan class "active" untuk halaman yang sedang aktif
          if (i === currentPage) {
              pageButton.classList.add("active"); // Tambahkan class aktif (untuk warna merah)
          }

          pageButton.addEventListener("click", function () {
              currentPage = i;
              loadProvinsiList(searchParams); // Muat data untuk halaman yang dipilih dengan parameter pencarian
          });

          paginationContainer.appendChild(pageButton);
      }

      // Tambahkan tombol "Next"
      const nextButton = document.createElement("button");
      nextButton.innerHTML = "&raquo;";
      nextButton.classList.add("page-button");
      nextButton.disabled = currentPage === totalPages;

      nextButton.addEventListener("click", function () {
          if (currentPage < totalPages) {
              currentPage++;
              loadProvinsiList(searchParams); // Muat data untuk halaman berikutnya dengan parameter pencarian
          }
      });

      paginationContainer.appendChild(nextButton);

      // Menambahkan total halaman di sebelah kanan panah
      const totalPagesSpan = document.createElement("span");
      totalPagesSpan.classList.add("total-pages-info");
      totalPagesSpan.innerText = `Total ${totalPages} pages`;
      paginationContainer.appendChild(totalPagesSpan);

      // Menambahkan tulisan "Go to page"
      const goToPageText = document.createElement("span");
      goToPageText.classList.add("go-to-page-text");
      goToPageText.innerText = " Go to page ";
      paginationContainer.appendChild(goToPageText);

      // Tambahkan input untuk memasukkan nomor halaman dan tombol Go
      const pageInput = document.createElement("input");
      pageInput.type = "number";
      pageInput.id = "page-input";
      pageInput.min = 1;
      pageInput.max = totalPages;
      pageInput.value = currentPage; // Set nilai input ke halaman saat ini
      pageInput.classList.add("page-input");

      const goButton = document.createElement("button");
      goButton.innerText = "Go";
      goButton.classList.add("go-button");

      goButton.addEventListener("click", function () {
          const pageNumber = parseInt(pageInput.value);

          if (!isNaN(pageNumber) && pageNumber > 0 && pageNumber <= totalPages) {
              currentPage = pageNumber;
              loadProvinsiList(searchParams); // Muat data untuk halaman yang dipilih dengan parameter pencarian
          } else {
              alert(`Silakan masukkan angka yang valid antara 1 dan ${totalPages}`);
          }
      });

      // Tambahkan input dan tombol Go ke container pagination
      paginationContainer.appendChild(pageInput);
      paginationContainer.appendChild(goButton);
  }

  // Fungsi untuk memperbarui informasi pagination
  function updatePaginationInfoProvinsi(totalData) {
      const startIndex = (currentPage - 1) * itemsPerPage + 1;
      const endIndex = Math.min(currentPage * itemsPerPage, totalData);

      const paginationInfo = document.getElementById("pagination-info-provinsi");
      paginationInfo.textContent = `Menampilkan ${startIndex} - ${endIndex} data dari total ${totalData} data.`;
  }

  // Event listener for the search button
  document
      .getElementById("cari-provinsi")
      .addEventListener("click", function () {
          currentPage = 1; // Reset to the first page each time a search is performed

          // Get the value of the search inputs and save them to the global searchParams variable
          searchParams = {
              Provinsi: document.getElementById("search-provinsi").value,
              ProvinsiAktif: document.getElementById("search-provinsiaktif").value, // Updated to get the combobox value
              IDProvinsiSatuSehat: document.getElementById(
                  "search-idprovinsi_satusehat"
              ).value,
              idprovinsiStatus: document.getElementById("sinkronMappingProvinsi")
                  .value,
          };

          loadProvinsiList(searchParams); // Load data with search parameters
      });

  // Fungsi untuk menampilkan modal loading
  function showLoading() {
      document.getElementById("loadingModalProvinsi").style.display = "block";
  }

  // Fungsi untuk menyembunyikan modal loading
  function hideLoading() {
      document.getElementById("loadingModalProvinsi").style.display = "none";
  }

  // Event listener untuk tombol Refresh
  document
      .getElementById("refresh-provinsi")
      .addEventListener("click", function () {
          document.getElementById("confirmRefreshModalProvinsi").style.display =
              "block"; // Tampilkan modal konfirmasi
      });

  // Event listener untuk tombol Proses di dalam modal konfirmasi refresh
  document
      .getElementById("prosesRefreshProvinsi")
      .addEventListener("click", function () {
          document.getElementById("confirmRefreshModalProvinsi").style.display =
              "none"; // Sembunyikan modal konfirmasi
          showLoading(); // Tampilkan loading

          // Proses Refresh
          setTimeout(() => {
              console.log("Tombol Refresh diklik dan konfirmasi disetujui");
              loadProvinsiList(searchParams); // Muat ulang data tanpa mengubah pencarian

              hideLoading(); // Sembunyikan loading setelah data dimuat
          }, 1000); // Tambahkan sedikit delay agar transisi loading terlihat
      });

  // Fungsi untuk reset state dan form setelah mapping selesai
  function resetMappingFormprovinsi() {
      // Reset checkbox dan form
      document.querySelectorAll(".select-checkbox").forEach((checkbox) => {
          checkbox.checked = false;
      });

      document
          .querySelectorAll(".kemenkes-provinsi-checkbox")
          .forEach((checkbox) => {
              checkbox.checked = false;
          });

      // Kosongkan isi kontainer hasil mapping
      const container = document.getElementById("sync-results-provinsi");
      if (container) {
          container.innerHTML = ""; // Bersihkan hasil mapping sebelumnya
      }

      // Kosongkan data obat yang dipilih
      selectedDataProvinsi = [];

      // Kembalikan tampilan halaman Mapping Obat Vmedis dan sembunyikan verifikasi
      $("#VerificationprovinsiContent").hide(); // Menghapus elemen verifikasi sebelumnya
      $("#header-provinsi").show(); // Tampilkan kembali header halaman utama
  }

  // Function to sanitize the medicine name (removes numbers and spaces, and adjusts case)
  function sanitizeNameprovinsi(name) {
      return name.replace(/[^a-zA-Z]/g, ''); // Only letters, lower case
  }
  
  





  async function fetchAccuracyprovinsi(firstString, secondString) {
      console.log(`Calculating accuracy between: "${firstString}" and "${secondString}"`);  // Debugging log
      try {
          const response = await fetch('calculateAccuracyProvinsi.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ firstString, secondString })
          });
  
          if (!response.ok) throw new Error(`Error fetching accuracy: ${response.status}`);
          const data = await response.json();
          return data.accuracy || 0;
      } catch (error) {
          console.error("Error fetching accuracy:", error);
          return 0; // Return 0 on error
      }
  }
  
  
  




  // Batch accuracy calculation to reduce requests

  async function calculateAccuraciesBatchprovinsi(selectedProvName, ProvinceData) {
      // Sanitize and standardize the selected province name
      const sanitizedSelectedNameProv = sanitizeNameprovinsi(selectedProvName).toLowerCase();
  
      // Process the province data in parallel to calculate accuracy
      const results = await Promise.all(ProvinceData.map(async (item) => {
              // Sanitize and standardize the Kemenkes province name
              const sanitizedItemNameProv = sanitizeNameprovinsi(item.name).toLowerCase();
  
              // Direct match for 100% accuracy
              let accuracy = sanitizedSelectedNameProv === sanitizedItemNameProv
                  ? 100
                  : await fetchAccuracyprovinsi(sanitizedSelectedNameProv, sanitizedItemNameProv);

                  return { ...item, accuracy: parseFloat(accuracy) };
              }));
              return results;
          }


              // Otherwise, fetch the calculated accuracy from the backend

  
  



  // Function to fetch Kemenkes province data from the API
  async function fetchProvinceData() {
      console.log("Fetching provinces data...");

      try {
          const response = await fetch("api_provinsi.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded",
              },
          });

          if (!response.ok) {
              console.error(
                  "Error fetching data from Kemenkes:",
                  response.status,
                  response.statusText
              );
              throw new Error("Gagal mengambil data dari Kemenkes");
          }

          const data = await response.json();

          // Assuming the API returns a list of provinces in `data`
          if (data.data && Array.isArray(data.data)) {
              console.log("Provinces data received:", data.data);
              return data.data;
          } else {
              console.warn("Unexpected data format:", data);
              return [];
          }
      } catch (error) {
          console.error("Error:", error);
          return [];
      }
  }

  // Function to process selected provinsi and fetch Kemenkes data
  async function processSelectedProvinsi(selectedDataProvinsi) {
      try {
          const ProvinceDataPromises = selectedDataProvinsi.map(Provinsi => fetchProvinceData(Provinsi.provinsi));
          const ProvinceDataResults = await Promise.all(ProvinceDataPromises);

          // Display verification content for selected provinces
          displayVerificationprovinsiContent(selectedDataProvinsi,ProvinceDataResults);
      } catch (error) {
          console.error(error);
          alert("Gagal mengambil data dari API Kemenkes");
      }
  }

  async function displayVerificationprovinsiContent(selectedDataProvinsi, ProvinceDataResults) {
      const provinsiMappingContentContainer = document.getElementById("provinsiMappingContentContainer");

      // Ensure the container exists
      if (!provinsiMappingContentContainer) {
          console.error("Element with ID 'provinsiMappingContentContainer' not found.");
          return;
      }

      // Remove previous content if it exists
      const previousVerificationprovinsiContent = document.getElementById("VerificationprovinsiContent");
      if (previousVerificationprovinsiContent) previousVerificationprovinsiContent.remove();

      // Process the data for verification
      const verificationProvItems = await Promise.all(selectedDataProvinsi.map(async (selectedProv, index) => {
              // Ensure provinsiDataResults[index] exists
              if (!ProvinceDataResults[index]) {
                  console.error(`No data found for provinsi index: ${index}`);
                  return ''; // Skip this entry if no data is available for this index
              }

              // Calculate accuracies for the current selected provinsi and Kemenkes data
              const dataWithAccuracy = await calculateAccuraciesBatchprovinsi(selectedProv.provinsi,ProvinceDataResults[index]);
              dataWithAccuracy.sort((a, b) => b.accuracy - a.accuracy);
              console.log("Data with Accuracy:", dataWithAccuracy);

              // Sort by accuracy in descending order


              // Create the list of Kemenkes provinces with accuracy data
              const kemenkesProvOptions = dataWithAccuracy.map((kemenkesProv, i) => `
                      <li>
                          <input type="checkbox" class="kemenkes-provinsi-checkbox" id="kemenkes-${index}-${i}" value="${kemenkesProv.name}" 
                          data-selected-index="${index}" data-kemenkes-prov='${JSON.stringify(kemenkesProv)}' />
                          ${kemenkesProv.accuracy % 1 === 0 ? `${kemenkesProv.accuracy}%` : `${kemenkesProv.accuracy.toFixed(2)}%`} - ${kemenkesProv.name} - ${kemenkesProv.code} - ${kemenkesProv.bps_code}
                      </li>
                  `)
                  .join(""); // Join the array into a single string

              return `
            <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                <div style="width: 45%;">
                    <h3>Provinsi Terpilih</h3>
                    <strong>${index + 1}.</strong> ${selectedProv.PROVINSI} - ${selectedProv.PROVINSIAKTIF
                  } - ${selectedProv.IDPROVINSISATUSEHAT}
                </div>
                <div style="width: 45%;">
                    <h3>Data Provinsi dari Kemenkes</h3>
                    ► ${selectedProv.PROVINSI}
                    <ul>${kemenkesProvOptions}</ul>
                </div>
            </div>
                <hr style="border: 1px solid #ddd; margin: 10px 0;">
            `;
          })
      );

      const VerificationprovinsiContent = `
        <div id="VerificationprovinsiContent" style="background-color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
            <h2 style="text-align: center;">Verifikasi Mapping Provinsi</h2>
            ${verificationProvItems.join("")}
            <button id="confirmMappingProvinsi" class="btn-confirm">Konfirmasi Mapping</button>
            <button id="cancelMappingProvinsi" class="btn-cancel">Batal</button>
        </div>
    `;

      // Gunakan verificationContent di sini
      $("#provinsiMappingContentContainer").append(VerificationprovinsiContent);
      $("#header-provinsi").hide();

      $(document)
          .off("click", "#confirmMappingProvinsi")
          .on("click", "#confirmMappingProvinsi", async function (event) {
              event.preventDefault();
              const mappedProvinces = [];

              selectedDataProvinsi.forEach((selectedProv, index) => {
                  const selectedCheckbox = document.querySelector(
                      `.kemenkes-provinsi-checkbox[data-selected-index="${index}"]:checked`
                  );
                  if (selectedCheckbox) {
                      const kemenkesProv = JSON.parse(
                          selectedCheckbox.getAttribute("data-kemenkes-prov")
                      );
                      mappedProvinces.push({
                          selected_provinsi: selectedProv,
                          mapped_kemenkes_provinsi: {
                              PROVINSI: kemenkesProv.name,
                              IDPROVINSISATUSEHAT: kemenkesProv.code,
                          }
                      });
                  }
              });

              // Debugging: Log the mappedMeds array
              console.log("Mapped Meds Array:", mappedProvinces);

              if (mappedProvinces.length > 0) {
                  try {
                      await updateVmedisDataProvince(mappedProvinces);

                      $("#VerificationprovinsiContent").hide();
                      $("#header-provinsi").show();

                      alert("Data berhasil diupdate ke Vmedis!");
                  } catch (error) {
                      console.error(error);
                      alert("Terjadi kesalahan saat mengupdate data ke Vmedis.");
                  }
              } else {
                  alert("Pilih setidaknya satu provinsi untuk dimapping.");
              }
          });

      $(document)
          .off("click", "#cancelMappingProvinsi")
          .on("click", "#cancelMappingProvinsi", function () {
              $("#VerificationprovinsiContent").hide();
              $("#header-provinsi").show();
          });
  }

  async function updateVmedisDataProvince(mappedProvinces) {
      try {
          const response = await fetch("update_provinsi.php", {
              method: 'POST',
              headers: {
                  "Content-Type": "application/json",
              },
              body: JSON.stringify({
                  mapped_province: mappedProvinces
              })
          });

          if (!response.ok) {
              console.error("Failed to update Vmedis data:", response.status, response.statusText);
              throw new Error("HTTP error: " + response.status);
          }

          const result = await response.json();
          console.log("Server Response:", result);

          if (result.success) {
              resetMappingFormprovinsi();
              loadProvinsiList(searchParams); // Reload table with updated data
              alert("Data berhasil diupdate ke Vmedis!");
          } else {
              alert("Gagal mengupdate data ke Vmedis.");
          }
      } catch (error) {
          console.error("Error during update:", error);
          alert("Terjadi kesalahan saat mengupdate data ke Vmedis.");
      }
  }






  // Event handlers and existing logic
  const matchingButtonProvinsi = document.getElementById("matching-provinsi");
  const confirmationModalprovinsi= document.getElementById(
      "confirmationModalProvinsi"
  );
  const confirmProcessButtonprovinsi = document.getElementById(
      "confirmProcessProvinsi"
  );
  const cancelProcessButtonprovinsi = document.getElementById("cancelProcessProvinsi");
  const provinsimappingContentContainer = document.getElementById(
      "provinsiMappingContentContainer"
  );

  matchingButtonProvinsi.onclick = function () {
      const checkboxes = document.querySelectorAll(".select-checkbox");
      let anyChecked = Array.from(checkboxes).some(
          (checkbox) => checkbox.checked
      );

      if (anyChecked) {
          confirmationModalprovinsi.style.display = "block";
      } else {
          alert("Silakan pilih obat terlebih dahulu.");
      }
  };

  confirmProcessButtonprovinsi.onclick = async function () {
      const selectedDataProvinsi = [];
      const checkboxes = document.querySelectorAll(".select-checkbox");

      checkboxes.forEach((checkbox, index) => {
          if (checkbox.checked) {
              const cells = document.querySelectorAll("#obat-list-provinsi tr")[index]
                  .cells;
              const PRovinsi = cells[1].textContent;
              const ProvinsiAktif = cells[2].textContent;
              const IDProvinsiSatuSehat = cells[3].textContent;
              if (PRovinsi && PRovinsi.trim() !== "") {
                  selectedDataProvinsi.push({
                      PROVINSI: PRovinsi.trim(),
                      PROVINSIAKTIF: ProvinsiAktif.trim(),
                      IDPROVINSISATUSEHAT: IDProvinsiSatuSehat.trim(),
                  });
              }
          }
      });
      if (selectedDataProvinsi.length > 0) {
          confirmationModalprovinsi.style.display = "none";

          // Ambil obat yang dicentang dari hasil mapping Kemenkes
          const mappedProvinces = [];

          document
              .querySelectorAll(".kemenkes-provinsi-checkbox:checked")
              .forEach((checkbox) => {
                  const kemenkesProv = JSON.parse(
                      checkbox.getAttribute("data-kemenkes-prov")
                  ); // Ambil data kemenkesMed dari atribut data-kemenkes-med
                  mappedProvinces.push({
                      provinsi: checkbox.value, // Nama obat dari checkbox
                      PROVINSI: kemenkesProv.name, // Kode KFA dari data Kemenkes
                      IDPROVINSISATUSEHAT: kemenkesProv.code, // Kode BPOM dari data Kemenkes
                  });
              });

          try {
              processSelectedProvinsi(selectedDataProvinsi);
          } catch (error) {
              console.error(error);
              alert("Gagal mengambil data dari API Kemenkes");
          }
      } else {
          alert("Pilih Provinsi terlebih dahulu untuk diproses.");
      }
  };

  cancelProcessButtonprovinsi.onclick = function () {
      confirmationModalprovinsi.style.display = "none";
  };

  window.onclick = function (event) {
      if (event.target == confirmationModalprovinsi) {
          confirmationModalprovinsi.style.display = "none";
      }
  };
});


function confirmDeleteProvinsi() {
  // Select all checkboxes with the class 'select-checkbox'
  const checkboxes = document.querySelectorAll('.select-checkbox');
  // Iterate through each checkbox and uncheck it
  checkboxes.forEach(checkbox => {
      checkbox.checked = false;
  });
  // Close the modal after unchecking
  closeModalProvinsi('deleteModal');
}

// Function to close the modal
function closeModal(modalId) {
  document.getElementById(modalId).style.display = 'none';
}
