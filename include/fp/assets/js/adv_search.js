jQuery(document).ready(function ($) {
  var selectedOptions = {};
  const advSearchBtn = $("#adv-search-btn");
  const trendingItem = $(".trending-searches-items");
  const activeFilters = $("#active-filters");
  let currentActivePage = 1;
  let lastActivePage = 0;
  let searchLastQuery = null;
  let searchLastFilters = null;

  $(document).on("click", ".search-filter-button", function () {
    const filterOptions = $("#filterOptions");
    const isExpanded = filterOptions.hasClass("expanded");
    $(".search-filter-button").html(!isExpanded ? '<i class="bi bi-x"></i> Reset' : '<i class="bi bi-filter"></i> Filters');
    if (!isExpanded) {
      filterOptions.css("display", "flex");
      filterOptions.css("visibility", "hidden");
      filterOptions.addClass("expanded");

      setTimeout(() => {
        filterOptions.css("visibility", "visible");
        filterOptions.css("opacity", 1);
      }, 300);
    } else {
      // Remove the class and hide after transition (same as before)
      filterOptions.css("opacity", 0);
      setTimeout(() => {
        filterOptions.removeClass("expanded");
        filterOptions.hide();
      }, 500);
    }
    if (!isExpanded) {
      // console.log("Resetting filters");
      resetFilters();
      //   fireSearchHandler();
    }
  });

  $(document).on("click", ".select-box", function (event) {
    const $icon = $(this).find("span i");
    const $optionsList = $(this).next(".options-list");
    if ($optionsList.is(":visible")) {
      event.preventDefault();
    } else {
      $icon.toggleClass("bi-caret-down-fill bi-caret-up-fill");
      $optionsList.slideToggle(300);
    }
  });

  $(document).on("mousedown", function (event) {
    if (!$(event.target).closest(".custom-multiselect").length || $(event.target).closest(".select-box").length) {
      $(".options-list").slideUp(300);
      $(".select-box span i").removeClass("bi-caret-up-fill").addClass("bi-caret-down-fill");
    }
  });

  $('.options-list input[type="checkbox"]').on("change", function () {
    collectFilterData();
    // fireSearchHandler();
  });

  $('.options-list input[type="radio"]').on("change", function () {
    collectFilterData();
    // fireSearchHandler();
  });

  advSearchBtn.on("click", function () {
    fireSearchHandler();
  });

  trendingItem.on("click", function () {
    // get its span text value
    let tempSearchQuery = $(this).find("span").text();
    // trim
    tempSearchQuery = tempSearchQuery.trim();
    // set the value to the search input
    $("#adv-search-input").val(tempSearchQuery);
    // fire search handler
    fireSearchHandler();
  });

  // Track ENter on input
  $("#adv-search-input").on("keyup", function (e) {
    if (e.key === "Enter") {
      fireSearchHandler();
    }
  });

  $(document).on("click", ".search-result-pagination-link", function (e) {
    e.preventDefault();
    if ($(this).data("page") === lastActivePage) {
      return;
    }
    lastActivePage = currentActivePage;
    currentActivePage = $(this).data("page");
    // alert(currentActivePage);
    $(".search-result-pagination-item").removeClass("active");
    $(this).parent().addClass("active");
    fireSearchHandler();
  });

  function resetFilters() {
    $(".custom-multiselect").each(function () {
      $(this).find('input[type="checkbox"]:checked').prop("checked", false);
      $(this).find('input[type="radio"]:checked').prop("checked", false);
      // collectFilterData();
      // fireSearchHandler();
      setTimeout(() => collectFilterData(), 100);
      setTimeout(() => fireSearchHandler(), 100);
    });
  }

  $(document).on("click", ".remove-filter-btn", function () {
    // RESET FILTERS
    resetFilters();
  });

  function collectFilterData() {
    selectedOptions = {};
    $(".custom-multiselect").each(function () {
      var category = $(this).find(".select-box span").first().data("tagid");
      var values = $(this)
        .find('input[type="checkbox"]:checked, input[type="radio"]:checked')
        .map(function () {
          return $(this).val();
        })
        .get();
      if (values.length > 0) {
        selectedOptions[category] = values;
      }
    });
    // console.log(selectedOptions);
  }

  function showLoading() {
    $(".search-adv-loader").html('<div class="spinner-wrapper"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
  }

  function hideLoading() {
    $(".search-adv-loader").empty();
  }

  function toggleSearchClick(enable = false) {
    if (enable) {
      // pointer-events: auto; opacity: 1;
      advSearchBtn.css("pointer-events", "auto");
      advSearchBtn.css("opacity", 1);
    } else {
      // pointer-events: none; opacity: 0.5;
      advSearchBtn.css("pointer-events", "none");
      advSearchBtn.css("opacity", 0.5);
    }
  }

  function fireSearchHandler() {
    const searchQuery = $("#adv-search-input").val();

    if (searchQuery === searchLastQuery && JSON.stringify(selectedOptions) === JSON.stringify(searchLastFilters) && currentActivePage === lastActivePage) {
      return;
    }

    // if (currentActivePage === lastActivePage) {
    //   return;
    // }

    // activeFilters html
    activeFilters.empty();
    // reset button if filters exist
    if (Object.keys(selectedOptions).length > 0) {
      activeFilters.append(
        '<i class="bi bi-x remove-filter-btn px-2 py-1 text-white cursor-pointer bg-red-700 absolute -top-3 right-2 text-xs rounded-3xl">RESET</i>'
      );
      let filterHtml = '<div class="flex flex-wrap justify-start items-center pt-5 pb-1 gap-3">';
      for (const [key, value] of Object.entries(selectedOptions)) {
        value.forEach((v) => {
          filterHtml +=
            '<div class="active-filter-item flex justify-between items-center px-3 py-2 bg-[#333] rounded-md" ' +
            'data-tagid="' +
            key +
            '" data-tagvalue="' +
            v +
            '"' +
            '><span class="text-white text-sm">' +
            v +
            "</span></div>";
        });
      }
      filterHtml += "</div>";
      activeFilters.append(filterHtml);
    }

    const oldResultsContainer = $("#search-results .search-results-list");
    oldResultsContainer.empty();
    showLoading();

    toggleSearchClick(false);

    // let activePage = $(".search-result-pagination-link").data("page") || 1;

    $.ajax({
      url: fp_asData.ajaxurl,
      method: "POST",
      dataType: "json",
      data: {
        action: "fp_perform_search",
        nonce: fp_asData.nonce,
        search: searchQuery,
        filters: selectedOptions,
        paged: currentActivePage,
      },
      success: function (response) {
        hideLoading();
        if (response.success) {
          searchLastQuery = searchQuery;
          searchLastFilters = selectedOptions;

          renderSearchResults(response.data.results);
          updatePagination(response.data.pagination);

          toggleSearchClick(true);
        } else {
          // console.error("Search failed: ", response.data);
          toggleSearchClick(true);
        }
      },
      error: function (xhr, status, error) {
        hideLoading();
        // console.error("AJAX Error: ", error);
        toggleSearchClick(true);
      },
    });
  }

  function updatePagination(pagination) {
    const $paginationContainer = $(".search-result-pagination-wrapper");
    $paginationContainer.empty();

    if (pagination.total_pages > 0) {
      const currentPage = pagination.current_page;
      const totalPages = pagination.total_pages;

      // Handle case where total pages are 1 or 2
      if (totalPages <= 2) {
        for (let i = 1; i <= totalPages; i++) {
          $paginationContainer.append(
            '<div class="search-result-pagination-item ' +
              (i === currentPage ? "active" : "") +
              '"><a href="#" class="search-result-pagination-link" data-page="' +
              i +
              '">' +
              i +
              "</a></div>"
          );
        }
      } else {
        // Number of pages to show before and after the current page
        const delta = 2;
        const range = [];
        const rangeWithDots = [];
        let l;

        for (let i = 1; i <= totalPages; i++) {
          if (i == 1 || i == totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
            range.push(i);
          }
        }

        for (let i of range) {
          if (l) {
            if (i - l === 2) {
              rangeWithDots.push(l + 1);
            } else if (i - l !== 1) {
              rangeWithDots.push("...");
            }
          }
          rangeWithDots.push(i);
          l = i;
        }

        // Render pagination buttons
        for (let i of rangeWithDots) {
          if (i === "...") {
            $paginationContainer.append(
              '<div class="search-result-pagination-item disabled"><span class="search-result-pagination-link">...</span></div>'
            );
          } else {
            $paginationContainer.append(
              '<div class="search-result-pagination-item ' +
                (i === currentPage ? "active" : "") +
                '"><a href="#" class="search-result-pagination-link" data-page="' +
                i +
                '">' +
                i +
                "</a></div>"
            );
          }
        }
      }
    }
  }

  function renderSearchResults(results) {
    const $resultsContainer = $("#search-results .search-results-list");
    $resultsContainer.empty();

    if (results.length === 0) {
      $resultsContainer.append("<p style='text-align: center; width: 100%'>No results found</p>");
      return;
    }

    results.forEach((result) => {
      const $item = $("<div>", {
        class: "search-results-list-item flex justify-start items-center flex-col w-[150px] md:w-[200px] overflow-hidden",
      });

      const $link = $("<a>", {
        href: result.p_link,
        class: "flex flex-col",
      });

      const $imgWrapper = $("<div>", {
        class: "overflow-hidden w-[150px] md:w-[200px] max-h-[250px] md:max-h-[300px]",
      });

      const $img = $("<img>", {
        src: result.thumb || "https://dummyimage.com/200x2:3/333/fff.jpg?text=NO+IMAGE",
        alt: "Poster",
        class:
          "object-cover hover:scale-110 transition-transform duration-300 w-[150px] md:w-[200px] max-h-[250px] md:max-h-[300px] min-h-[250px] md:min-h-[300px]",
      });

      const $content = $("<div>", {
        class: "search-results-list-item-content inline-flex flex-col",
      });

      const $title = $("<h3>", {
        class: "adv-result-title text-sm font-semibold text-white text-center p-2 text-wrap",
        text: $("<div>").html(result.title).text(),
      });

      $content.append($title);
      $imgWrapper.append($img);
      $link.append($imgWrapper, $content);
      $item.append($link);
      $resultsContainer.append($item);
    });
  }

  fireSearchHandler();
});
