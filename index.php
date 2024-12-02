<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AwsmGallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #1E1E1E;
            color: #E0E0E0;
        }

        .top-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #2D2D2D;
            border-radius: 8px;
        }

        .stats {
            font-size: 0.9em;
            color: #A0A0A0;
        }

        .view-controls, .sort-controls {
            display: flex;
            gap: 10px;
        }

        .control-button {
            background: #3870C9;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .control-button:hover {
            background: #2D5BA6;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            grid-gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }

        .container.list-view {
            display: flex;
            flex-direction: column;
        }

        .container.list-view .image {
            display: flex;
            height: 100px;
            align-items: center;
        }

        .container.list-view .image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .container.list-view .image-info {
            position: relative;
            opacity: 1;
            flex: 1;
            background: none;
            padding: 0 20px;
        }

        .image {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.8);
            background: #2D2D2D;
        }

        .image img {
            display: block;
            width: 100%;
            height: auto;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .image:hover img {
            transform: scale(1.05);
        }

        .image-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.7);
            padding: 10px;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 3;
            font-size: 0.9em;
        }

        .image:hover .image-info {
            opacity: 1;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal.show {
            opacity: 1;
        }

        .modal-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .modal-image {
            max-width: 90%;
            max-height: 80%;
        }

        .modal-controls {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .slideshow-controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(45, 45, 45, 0.9);
            padding: 10px;
            border-radius: 5px;
            display: flex;
            gap: 10px;
        }

        .keyboard-shortcuts {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #2D2D2D;
            padding: 20px;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .keyboard-shortcuts table {
            border-collapse: collapse;
        }

        .keyboard-shortcuts td {
            padding: 5px 10px;
        }

        .keyboard-shortcuts .key {
            background: #3870C9;
            padding: 2px 8px;
            border-radius: 3px;
            margin-right: 10px;
        }

        .images-per-page {
            padding: 5px;
            background: #2D2D2D;
            color: #E0E0E0;
            border: 1px solid #3870C9;
            border-radius: 4px;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination button {
            background-color: #3870C9;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 0 5px;
        }

        .pagination button:hover {
            background-color: #2D5BA6;
        }

        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            color: #A0A0A0;
        }

        .footer a {
            color: #3870C9;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="top-controls">
        <div class="stats">
            <?php
            $totalImages = count(glob("*.{jpg,jpeg,png,gif}", GLOB_BRACE));
            echo "Total Images: $totalImages";
            ?>
        </div>
        <div class="sort-controls">
            <button class="control-button" onclick="sortImages('date')">
                <i class="fas fa-calendar"></i> Sort by Date
            </button>
            <button class="control-button" onclick="sortImages('size')">
                <i class="fas fa-weight"></i> Sort by Size
            </button>
            <select class="images-per-page" onchange="changeImagesPerPage(this.value)">
                <option value="25">25 per page</option>
                <option value="50" selected>50 per page</option>
                <option value="100">100 per page</option>
                <option value="200">200 per page</option>
            </select>
        </div>
        <div class="view-controls">
            <button class="control-button" onclick="toggleView('grid')">
                <i class="fas fa-th"></i> Grid
            </button>
            <button class="control-button" onclick="toggleView('list')">
                <i class="fas fa-list"></i> List
            </button>
            <button class="control-button" onclick="toggleShortcuts()">
                <i class="fas fa-keyboard"></i> Shortcuts
            </button>
        </div>
    </div>

    <div class="container">
        <?php
        $imagesPerPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 50;
        $files = glob("*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        
        // Sorting
        $sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'none';
        if ($sortBy === 'date') {
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
        } elseif ($sortBy === 'size') {
            usort($files, function($a, $b) {
                return filesize($b) - filesize($a);
            });
        }

        $totalImages = count($files);
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $startIndex = ($currentPage - 1) * $imagesPerPage;
        $endIndex = min($startIndex + $imagesPerPage, $totalImages);

        for ($i = $startIndex; $i < $endIndex; $i++) {
            $fileSize = round(filesize($files[$i]) / 1024, 2);
            $creationDate = date("d-m-Y H:i:s", filemtime($files[$i]));
            echo '<div class="image" data-src="' . $files[$i] . '" onclick="showImageModal(\'' . $files[$i] . '\')">
                    <img src="' . $files[$i] . '" alt="' . $files[$i] . '" loading="lazy">
                    <div class="image-info">
                        <span>' . $files[$i] . '</span><br>
                        <span>' . $fileSize . ' KB</span><br>
                        <span>' . $creationDate . '</span>
                    </div>
                  </div>';
        }
        ?>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <img id="modalImage" class="modal-image" src="" alt="Modal Image">
            <div class="modal-controls">
                <button class="control-button" onclick="downloadImage()">
                    <i class="fas fa-download"></i> Download
                </button>
                <button class="control-button" onclick="copyImageLink()">
                    <i class="fas fa-link"></i> Copy Link
                </button>
                <button class="control-button" onclick="toggleSlideshow()">
                    <i class="fas fa-play"></i> Slideshow
                </button>
            </div>
        </div>
    </div>

    <div class="slideshow-controls" style="display: none;">
        <button class="control-button" onclick="adjustSlideshowSpeed('slower')">
            <i class="fas fa-minus"></i>
        </button>
        <span id="slideshow-speed">3s</span>
        <button class="control-button" onclick="adjustSlideshowSpeed('faster')">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <div class="keyboard-shortcuts">
        <h3>Keyboard Shortcuts</h3>
        <table>
            <tr><td><span class="key">←</span></td><td>Previous Image</td></tr>
            <tr><td><span class="key">→</span></td><td>Next Image</td></tr>
            <tr><td><span class="key">Esc</span></td><td>Close Modal</td></tr>
            <tr><td><span class="key">Space</span></td><td>Start/Stop Slideshow</td></tr>
            <tr><td><span class="key">?</span></td><td>Toggle Shortcuts</td></tr>
        </table>
    </div>

    <div class="pagination">
        <?php
        $totalPages = ceil($totalImages / $imagesPerPage);
        if ($currentPage > 1) {
            echo '<a href="?page=' . ($currentPage - 1) . '&perPage=' . $imagesPerPage . '&sort=' . $sortBy . '"><button>Previous</button></a>';
        }
        if ($currentPage < $totalPages) {
            echo '<a href="?page=' . ($currentPage + 1) . '&perPage=' . $imagesPerPage . '&sort=' . $sortBy . '"><button>Next</button></a>';
        }
        ?>
    </div>
      <script>
          let slideshowInterval = null;
          let slideshowSpeed = 3000;
          let currentView = 'grid';

          function showImageModal(imageSrc) {
              const modal = document.getElementById("myModal");
              const modalImg = document.getElementById("modalImage");
              modal.style.display = "block";
              modalImg.src = imageSrc;
              setTimeout(() => modal.classList.add('show'), 10);
          }

          document.getElementById('myModal').addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-content')) {
                  closeModal();
              }
          });

          function closeModal() {
              const modal = document.getElementById("myModal");
              modal.classList.remove('show');
              setTimeout(() => modal.style.display = "none", 300);
              stopSlideshow();
          }

          function downloadImage() {
              const link = document.createElement('a');
              link.href = document.getElementById('modalImage').src;
              link.download = link.href.split('/').pop();
              link.click();
          }
            function copyImageLink() {
                const imageName = document.getElementById('modalImage').src.split('/').pop();
                const imageUrl = window.location.origin + window.location.pathname.replace('index.php', '') + imageName;
              
                // Using try-catch to handle clipboard operations
                try {
                    // For modern browsers
                    navigator.clipboard.writeText(imageUrl)
                        .then(() => alert('Image link copied to clipboard!'))
                        .catch(() => {
                            // Fallback method using a temporary textarea
                            const textarea = document.createElement('textarea');
                            textarea.value = imageUrl;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textarea);
                            alert('Image link copied to clipboard!');
                        });
                } catch (err) {
                    // Final fallback
                    prompt('Copy this link:', imageUrl);
                }
            }

          function toggleSlideshow() {
              if (slideshowInterval) {
                  stopSlideshow();
              } else {
                  startSlideshow();
              }
          }

          function startSlideshow() {
              document.querySelector('.slideshow-controls').style.display = 'flex';
              slideshowInterval = setInterval(showNextImage, slideshowSpeed);
          }

          function stopSlideshow() {
              document.querySelector('.slideshow-controls').style.display = 'none';
              clearInterval(slideshowInterval);
              slideshowInterval = null;
          }

          function showNextImage() {
              const images = document.querySelectorAll('.image');
              const currentSrc = document.getElementById('modalImage').src;
              let found = false;
              let next = null;

              for (let i = 0; i < images.length; i++) {
                  if (found) {
                      next = images[i];
                      break;
                  }
                  if (images[i].dataset.src === currentSrc.split('/').pop()) {
                      found = true;
                  }
              }

              if (!next && found) {
                  const nextPageLink = document.querySelector('.pagination a:last-child');
                  if (nextPageLink) {
                      window.location.href = nextPageLink.href;
                  } else {
                      stopSlideshow();
                  }
              } else if (next) {
                  showImageModal(next.dataset.src);
              }
          }

          function adjustSlideshowSpeed(direction) {
              if (direction === 'slower' && slideshowSpeed < 10000) {
                  slideshowSpeed += 500;
              } else if (direction === 'faster' && slideshowSpeed > 1000) {
                  slideshowSpeed -= 500;
              }
              document.getElementById('slideshow-speed').textContent = (slideshowSpeed/1000) + 's';
              if (slideshowInterval) {
                  stopSlideshow();
                  startSlideshow();
              }
          }

          function toggleView(view) {
              currentView = view;
              const container = document.querySelector('.container');
              container.className = 'container ' + (view === 'list' ? 'list-view' : '');
          }

          function sortImages(method) {
              window.location.href = `?sort=${method}&page=1&perPage=${new URLSearchParams(window.location.search).get('perPage') || 50}`;
          }

          function changeImagesPerPage(value) {
              window.location.href = `?page=1&perPage=${value}&sort=${new URLSearchParams(window.location.search).get('sort') || 'none'}`;
          }

          function toggleShortcuts() {
              const shortcuts = document.querySelector('.keyboard-shortcuts');
              shortcuts.style.display = shortcuts.style.display === 'none' ? 'block' : 'none';
          }

          document.addEventListener('keydown', function(e) {
              const modal = document.getElementById('myModal');
            
              // Modal is open - handle image navigation
              if (modal.style.display === 'block') {
                  if (e.key === 'ArrowRight') {
                      e.preventDefault();
                      const images = document.querySelectorAll('.image');
                      const currentSrc = document.getElementById('modalImage').src;
                      let found = false;
                    
                      for (let i = 0; i < images.length; i++) {
                          if (found) {
                              showImageModal(images[i].dataset.src);
                              return;
                          }
                          if (images[i].dataset.src === currentSrc.split('/').pop()) {
                              found = true;
                          }
                      }
                      // If we're at the last image, go to next page
                      document.querySelector('.pagination a:last-child')?.click();
                  }
                  if (e.key === 'ArrowLeft') {
                      e.preventDefault();
                      const images = document.querySelectorAll('.image');
                      const currentSrc = document.getElementById('modalImage').src;
                    
                      for (let i = 0; i < images.length; i++) {
                          if (images[i].dataset.src === currentSrc.split('/').pop() && i > 0) {
                              showImageModal(images[i-1].dataset.src);
                              return;
                          }
                      }
                      // If we're at the first image, go to previous page
                      document.querySelector('.pagination a:first-child')?.click();
                  }
              } 
              // Modal is closed - handle page navigation
              else {
                  if (e.key === 'ArrowRight') document.querySelector('.pagination a:last-child')?.click();
                  if (e.key === 'ArrowLeft') document.querySelector('.pagination a:first-child')?.click();
              }
            
              // Other keyboard shortcuts remain the same
              if (e.key === 'Escape') {
                  closeModal();
                  document.querySelector('.keyboard-shortcuts').style.display = 'none';
              }
              if (e.key === ' ') {
                  e.preventDefault();
                  if (modal.style.display === 'block') {
                      toggleSlideshow();
                  }
              }
              if (e.key === '?') {
                  toggleShortcuts();
              }
          });
      </script>
    <div class="footer">
    Created by Jay Dee | <a href="https://github.com/CptAwsm" target="_blank">GitHub</a>
    </div>
  </body>
</html>
