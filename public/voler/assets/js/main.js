(() => {
  for (
    var e = document.querySelectorAll(".sidebar-item.has-sub"),
      t = function () {
        var t = e[r];
        e[r]
          .querySelector(".sidebar-link")
          .addEventListener("click", function (e) {
            e.preventDefault();
            var r = t.querySelector(".submenu");
            r.classList.contains("active")
              ? r.classList.remove("active")
              : r.classList.add("active");
          });
      },
      r = 0;
    r < e.length;
    r++
  )
    t();

  // SIDEBAR TOGGLE — FIXED (was commented out)
  var a = document.querySelectorAll(".sidebar-toggler");
  for (r = 0; r < a.length; r++) {
    a[r].addEventListener("click", function (e) {
      e.preventDefault();
      var sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("active");

      // Mobile: add overlay when sidebar is open
      if (window.innerWidth < 768) {
        var overlay = document.getElementById("sidebarOverlay");
        if (sidebar.classList.contains("active")) {
          if (!overlay) {
            overlay = document.createElement("div");
            overlay.id = "sidebarOverlay";
            overlay.className = "sidebar-overlay active";
            overlay.addEventListener("click", function () {
              sidebar.classList.remove("active");
              overlay.classList.remove("active");
              setTimeout(function () { overlay.remove(); }, 300);
            });
            document.body.appendChild(overlay);
          } else {
            overlay.classList.add("active");
          }
        } else if (overlay) {
          overlay.classList.remove("active");
          setTimeout(function () { overlay.remove(); }, 300);
        }
      }
    });
  }

  // Prevent touch scroll propagation from sidebar (iOS fix)
  var sidebarWrapper = document.querySelector(".sidebar-wrapper");
  if (sidebarWrapper) {
    sidebarWrapper.addEventListener("touchmove", function (e) {
      e.stopPropagation();
    }, { passive: true });
  }

  if ("function" == typeof PerfectScrollbar && window.innerWidth >= 768) {
    var c = document.querySelector(".sidebar-wrapper");
    new PerfectScrollbar(c);
  }

  // On load: hide sidebar on mobile
  (window.onload = function () {
    var e = window.innerWidth;
    if (e < 768) {
      document.getElementById("sidebar").classList.remove("active");
    }
    feather.replace();
  });

  // On resize: handle sidebar state (debounced to avoid iOS scroll-triggered resize)
  var resizeTimer;
  window.addEventListener("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      var sidebar = document.getElementById("sidebar");
      var overlay = document.getElementById("sidebarOverlay");
      if (window.innerWidth >= 768) {
        sidebar.classList.add("active");
        if (overlay) {
          overlay.remove();
        }
      }
      // Don't auto-close sidebar on mobile resize — only open on desktop
    }, 150);
  });
})();
