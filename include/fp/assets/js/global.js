jQuery(document).ready(function ($) {
  const $searchInput = $('.head-main-nav input[type="text"]');
  const $searchButton = $("#search-pc-btn");
  const $filterIcon = $("#header-filter-icon");
  const $sSearchTip = $("#search-s-icon");

  const resultHead = $("#results-head");
  const $pcSearchResults = $("#pc-search-result");

  const $mobileMenuToggle = $("#mobile-menu-toggle");
  const $mobileMenu = $("#menu-mobile-primary-content");

  const $mobileSearchContainer = $("#mobile-search-container");

  const advSearchInput = $("#adv-search-input");

  // const home_url = fp_sData.home_url;

  let lastQuery = null;
  let searchQuery = "";
  let inputSearchTimer = null;
  let searchTimer = null;
  let searchStatus = false;

  const $menuToggle = $("#secondary-menu-toggle");
  const $menuContent = $("#menu-secondary-content");

  $mobileMenuToggle.on("click", function () {
    closeMobileMenu();
  });

  // close menu when user scroll/touch outside of the menu
  $(document).on("click", function (event) {
    if (!$(event.target).closest("#menu-mobile-primary-content").length && $mobileMenu.css("maxHeight") !== "0px") {
      closeMobileMenu();
    }
  });

  $mobileSearchContainer.find("input[type='text']").on("keyup", function (e) {
    searchQuery = $(this).val();
    // console.log("Event key: ", e.key);
    // console.log("Search query: ", searchQuery);
    if (e.key === "Enter") {
      redirectUserSearch();
      return;
    }
    // console.log("LIVE search value: ", searchQuery);
    if (inputSearchTimer) clearTimeout(inputSearchTimer);
    inputSearchTimer = setTimeout(function () {
      fireSearchHandler("m");
    }, 500);
  });

  $(document).on("click", function (event) {
    if (!$(event.target).closest("#mobile-search-container").length && $mobileSearchContainer.css("opacity") === "1") {
      hideSearchContainer();
    }
  });

  function hideSearchContainer() {
    $mobileSearchContainer.css("opacity", "0");
    // search-icon
    var searchIcon = document.getElementById("search-icon");
    searchIcon.classList.remove("bi-x");
    searchIcon.classList.add("bi-search");

    setTimeout(function () {
      $mobileSearchContainer.addClass("hidden");
    }, 500);
  }

  function closeMobileMenu() {
    if ($mobileMenu.css("maxHeight") === "0px" || $mobileMenu.css("maxHeight") === "") {
      $mobileMenu.css("visibility", "hidden");
      $mobileMenu.removeClass("hidden");
      $mobileMenuToggle.removeClass("bi-list").addClass("bi-x");

      setTimeout(function () {
        $mobileMenu.css("maxHeight", "500px");
        $mobileMenu.css("visibility", "visible");
      }, 300);
    } else {
      $mobileMenu.css("maxHeight", "0");
      $mobileMenuToggle.removeClass("bi-x").addClass("bi-list");
      setTimeout(function () {
        $mobileMenu.css("visibility", "hidden");
        $mobileMenu.addClass("hidden");
      }, 300);
    }
  }

  $searchInput.on("focus", function () {
    $(this).removeClass("w-32").addClass("w-72");
    $filterIcon.removeClass("hidden");
    $sSearchTip.addClass("hidden");
    $pcSearchResults.removeClass("hidden");
    // console.log("search input focused");
  });

  $searchInput.on("keyup", function (e) {
    searchQuery = $(this).val();
    if (e.key === "Enter") {
      redirectUserSearch();
      return;
    }
    // console.log("LIVE search value: ", searchQuery);
    if (inputSearchTimer) clearTimeout(inputSearchTimer);
    inputSearchTimer = setTimeout(function () {
      fireSearchHandler("p");
    }, 500);
  });

  $searchInput.on("blur", function () {
    setTimeout(() => {
      if (!$(document.activeElement).is($searchInput) && !$(document.activeElement).is($filterIcon) && !$(document.activeElement).is($searchButton)) {
        if ($searchInput.hasClass("w-72")) {
          $searchInput.removeClass("w-72").addClass("w-32");
          $filterIcon.addClass("hidden");
          $sSearchTip.removeClass("hidden");
          $pcSearchResults.addClass("hidden");
        }
      }
    }, 100);
  });

  // ┌────────────────────────────┐
  // │ Search Input Function   │
  // └────────────────────────────┘
  $searchButton.on("mousedown", function (e) {
    e.preventDefault();
    redirectUserSearch();
  });

  function redirectUserSearch() {
    // searchQuery = $searchInput.val();

    if (searchQuery.length < 2) {
      // console.log("Enter at least 2 characters.");
      return;
    }
    // console.log("search value: ", searchQuery);

    // Redirect user to search page
    let searchUrl = fp_sData.home_url + "?s=" + searchQuery;
    // console.log("searchUrl: ", searchUrl);
    window.location.href = searchUrl;
  }

  $filterIcon.on("mousedown", function (e) {
    e.preventDefault();
  });

  // on press 's' key, focus on search input
  $(document).on("keydown", function (e) {
    // make sure advance search input is not focused
    // if (e.key === "s" && !$(document.activeElement).is($searchInput) && !$(document.activeElement).is(advSearchInput)) {
    if (
      e.key === "s" &&
      !$(document.activeElement).is('input, textarea, [contenteditable="true"]') &&
      !$(document.activeElement).is($searchInput) &&
      !$(document.activeElement).is(advSearchInput)
    ) {
      e.preventDefault();
      $searchInput.focus();
    }

    // on press 'esc' key, unfocus search input
    if (e.key === "Escape" && $(document.activeElement).is($searchInput)) {
      $searchInput.blur();
    }

    // on press 'esc' key, also for secondary menu
    if (e.key === "Escape" && $menuContent.css("maxHeight") !== "0px") {
      $menuContent.css("maxHeight", "0");
      setTimeout(function () {
        $menuContent.addClass("hidden");
      }, 500); // Match this to the transition duration
    }
  });

  $menuContent.css({
    transition: "max-height 0.5s ease-in-out",
    maxHeight: "0",
  });

  // Toggle menu on button click
  $menuToggle.on("click", function () {
    if ($menuContent.css("maxHeight") === "0px" || $menuContent.css("maxHeight") === "") {
      $menuContent.removeClass("hidden");
      setTimeout(function () {
        $menuContent.css("maxHeight", "450px");
      }, 0);
    } else {
      $menuContent.css("maxHeight", "0");
      setTimeout(function () {
        $menuContent.addClass("hidden");
      }, 500);
    }
  });

  $("#header-menu-2 li:has(ul)").on("click", function (e) {
    e.stopPropagation();
    const $submenu = $(this).children("ul");
    if ($submenu.is(":visible")) {
      $submenu.slideUp(300);
      $(this).removeClass("active");
    } else {
      $submenu.slideDown(300);
      $(this).addClass("active");
    }
  });

  $(document).on("click", function (event) {
    if (!$(event.target).closest("#menu-secondary-main").length && $menuContent.css("maxHeight") !== "0px") {
      $menuContent.css("maxHeight", "0");
      setTimeout(function () {
        $menuContent.addClass("hidden");
      }, 500); // Match this to the transition duration
    }
  });

  function fireSearchHandler(resultContainer) {
    // console.log("searchStatus: ", searchStatus);

    const containers = {
      m: $("#m-results"),
      p: $("#p-results"),
    };

    const currentContainer = containers[resultContainer];

    // console.log("currentContainer: ", currentContainer);

    if (searchQuery.length < 2 || (searchQuery.length < 2 && !searchStatus)) {
      if (!resultHead.hasClass("hidden")) resultHead.addClass("hidden");
      searchStatus = false;
      currentContainer.html('<p style="text-align: center;"><i class="bi bi-info-circle"></i> Enter at least 2 characters.</p>');
      return;
    } else if (searchQuery.length < 2 && searchStatus) {
      return;
    }

    // var filterData = collectFilterData();

    if (searchQuery === lastQuery && searchStatus) {
      // console.log("Same query and filters, skipping search");
      return;
    }

    // console.log("Sending search request...");

    lastQuery = searchQuery;

    currentContainer.html('<div class="spinner-wrapper"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');

    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      $.ajax({
        url: fp_sData.ajaxurl,
        method: "POST",
        data: {
          action: "fp_perform_search",
          nonce: fp_sData.nonce,
          search: searchQuery,
        },
        success: function (response) {
          if (response.success) {
            resultHead.removeClass("hidden");
            updateSearchResults(response.data.results, currentContainer);
          } else {
            currentContainer.html("<p>No results found.</p>");
            searchStatus = false;
          }
        },
        error: function () {
          currentContainer.html("<p>Error retrieving results.</p>");
          searchStatus = false;
        },
      });
    }, 1000);
  }

  function updateSearchResults(results, resultsContainer) {
    if (resultsContainer === undefined) {
      resultsContainer = $(".results");
    }
    // var resultsContainer = $(".results");
    resultsContainer.empty(); // Clear previous results

    if (results.length > 0) {
      results.forEach(function (result) {
        var resultElement = $("<div>", {
          class: "search-result-item",
          css: {
            // display: "flex",
            // gap: "1rem",
            // padding: "0.5rem 0.5rem",
          },
        });

        var linkWrapper = $("<a>", {
          href: result.p_link,
          css: {
            display: "flex",
          },
        });

        var imageContainer = $("<div>", {
          class: "search-result-item-image",
          css: {
            maxWidth: "50px",
            aspectRatio: "2:3",
            overflow: "clip",
          },
        }).append(
          $("<img>", {
            src: result.thumb, // assuming result.thumb has the image URL
            alt: result.title,
          })
        );

        var detailsContainer = $("<div>", {
          class: "search-result-item-details",
          css: {
            // display: "flex",
            // flex: 1,
            // flexDirection: "column",
            // gap: "0.2rem",
            // textAlign: "left",
            // padding: "0.1rem 0.3rem",
            // justifyContent: "center",
            // alignItems: "flex-start",
          },
        }).append(
          $("<span>", {
            text: $("<div>").html(result.title).text(),
            class: "search-result-item-title",
          })
        );
        var metaDataContainer = $("<div>", {
          class: "search-result-item-details-meta",
          css: {
            display: "flex",
            justifyContent: "flex-start",
            alignItems: "center",
            columnGap: "1rem",
            fontSize: "0.8rem", // Example size, adjust as needed
            flexWrap: "wrap",
            fontWeight: 600,
            paddingLeft: "0.3rem",
            color: "#fff", // Example color, adjust as needed
          },
        });

        // Append multiple metadata spans
        metaDataContainer.append(
          $("<span>", {
            text: result.post_type === "movie" ? "Movie" : "TV",
            css: { display: "flex", alignItems: "center" },
            // }).prepend($("<i>", { class: "bi bi-play-btn" })),
            // class = search-result-item-film-icon
          }).prepend($("<img>", { src: fp_sData.icon_film, alt: result.post_type, class: "search-result-item-meta-icon" })),
          $("<span>", {
            text: result.vote,
            css: { display: "flex", alignItems: "center" },
          }).prepend($("<i>", { class: "bi bi-star" })),
          $("<span>", {
            text: result.r_date,
            css: { display: "flex", alignItems: "center" },
            // }).prepend($("<i>", { class: "bi bi-calendar2-event" }))
          }).prepend($("<img>", { src: fp_sData.icon_calendar, alt: result.r_date, class: "search-result-item-meta-icon" }))
        );

        detailsContainer.append(metaDataContainer);
        // Append all containers to link wrapper
        linkWrapper.append(imageContainer).append(detailsContainer);

        // Append link wrapper to the result item
        resultElement.append(linkWrapper);

        // Append result item to the results container
        resultsContainer.append(resultElement);
      });
      searchStatus = true;
    } else {
      resultsContainer.html("<p style='text-align: center;'><i class='bi bi-info-circle'></i> No results found.</p>");
      searchStatus = false;
    }
  }


  // ┌───────────────────────┐
  // │ Social SHARE ICONS  │
  // └───────────────────────┘
  const social_shareBtn = $(".share_btn");
  const social_toggleButton = $(".share_toggle_button");
  social_shareBtn.on("click", function () {
    social_toggleButton.toggleClass("active");
  });

  // HIDE WHEN CLICK OUTSIDE
  $(document).on("click", function (event) {
    if (!$(event.target).closest(".share_btn").length) {
      social_toggleButton.removeClass("active");
    }
  });


});

document.getElementById("search-icon").addEventListener("click", function (event) {
  event.stopPropagation();
  var searchContainer = document.getElementById("mobile-search-container");
  var searchInput = searchContainer.querySelector("input");
  var init_searchContainer = document.getElementById("search-icon");
  if (searchContainer.classList.contains("hidden")) {
    searchContainer.classList.remove("hidden");
    searchContainer.classList.add("flex");
    // replace class bi-search with bi-x
    init_searchContainer.classList.remove("bi-search");
    init_searchContainer.classList.add("bi-x");

    setTimeout(function () {
      searchContainer.style.opacity = "1";
      searchInput.focus();
    }, 10);
  } else {
    init_searchContainer.classList.remove("bi-x");
    init_searchContainer.classList.add("bi-search");
    searchContainer.style.opacity = "0";
    setTimeout(function () {
      searchContainer.classList.remove("flex");
      searchContainer.classList.add("hidden");
    }, 500);
  }
});
