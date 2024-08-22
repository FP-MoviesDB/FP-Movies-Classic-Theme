jQuery(document).ready(function ($) {
  const defaultContentType = [
    { name: "Movie", slug: "movie" },
    { name: "Series", slug: "series" },
    { name: "Both", slug: "both" },
  ];
  // console.log("LOCAL VAR: ", fp_Data);

  function toggleSocialList() {
    let showSocialValue = $("#show-social").is(":checked");

    if (showSocialValue) {
      $("#social-list").show().css("display", "flex");
      $("#show-social-base-wrapper").removeClass("hidden").css("display", "flex");
    } else {
      $("#social-list").hide();
      $("#show-social-base-wrapper").hide();
    }
  }

  toggleSocialList();

  $("#show-social").on("change", function () {
    toggleSocialList();
  });

  let allData = {
    basic_settings: {},
    customize_settings: {
      homepage_layout: [],
      single_layout: [],
    },
  };

  function gatherAllSettings() {
    let b_settings = gatherBasicSettings();
    let c_settings = gatherCustomizeData();
    let s_settings = gatherSinglePageData();

    allData.basic_settings = b_settings;
    allData.customize_settings.homepage_layout = c_settings;
    allData.customize_settings.single_layout = s_settings;

    return allData;
  }

  // key is the id of the tab, value is the id of the content to show
  let itemList = {
    "#select-basic": "#fp-basic-settings",
    "#select-customize": "#fp-customize-settings",
    "#select-other": "#fp-other-settings",
  };

  Object.keys(itemList).forEach((key) => {
    let value = itemList[key];

    $(key).on("click", function () {
      Object.keys(itemList).forEach((item) => {
        $(itemList[item]).hide();
        $(item).removeClass("active");
      });
      $(value).show().css("display", "flex");

      $(key).addClass("active");
    });
  });

  function gatherBasicSettings() {
    let footer_text_val = $("#footer-text").val().trim();
    // console.log("ORIGINAL FOOTER TEXT: ", footer_text_val);
    // footer_text_val = btoa(footer_text_val);
    footer_text_val = utf8ToBase64(footer_text_val);
    let settings = {
      logo: $("#logo-url-relative-path").val(),
      favicon: $("#favicon-url-relative-path").val(),
      max_width: $("#max-width").val(),
      show_social: $("#show-social").is(":checked") ? true : false,
      social_base_icon: $("#social-text-icon").val(),
      social_list: [],
      footer_text: footer_text_val,
    };

    // console.log("ENCODED FOOTER TEXT: ", footer_text_val);

    $(".single-social-container").each(function () {
      let socialItem = {
        icon: $(this).find(".social-icon").val(),
        title: $(this).find(".social-title").val(),
        color: $(this).find(".social-color").val(),
        link: $(this).find(".social-link").val(),
      };
      if (socialItem.title && socialItem.link) {
        settings.social_list.push(socialItem);
      }
    });

    return settings;
  }

  function removeMedia(buttonId, inputId, imgID) {
    $(buttonId).on("click", function (e) {
      e.preventDefault();
      $(inputId).val("");
      $(imgID).attr("src", "");
    });
  }

  removeMedia("#remove-logo", "#logo-url-relative-path", "#fp-logo-p");
  removeMedia("#remove-favicon", "#favicon-url-relative-path", "#fp-fav-p");

  function handleMediaUpload(buttonId, inputId, imgID) {
    $(buttonId).on("click", function (e) {
      e.preventDefault();

      let mediaUploader = wp
        .media({
          title: "Select Image",
          button: {
            text: "Use this image",
          },
          multiple: false,
        })
        .on("select", function () {
          let attachment = mediaUploader.state().get("selection").first().toJSON();
          $(inputId).val(attachment.url);
          $(imgID).attr("src", attachment.url);
        })
        .open();
    });
  }

  // Logo Upload
  handleMediaUpload("#logo-uploader", "#logo-url-relative-path", "#fp-logo-p");

  // Favicon Upload
  handleMediaUpload("#favicon-uploader", "#favicon-url-relative-path", "#fp-fav-p");

  $("#max-width").on("input", function () {
    let maxWidth = parseInt($(this).val());
    let maxWidthText = $("#max-width-text");

    maxWidthText.text(maxWidth + "px");
  });

  // Handle Add New Social Entry
  $("#add-new-social").on("click", function () {
    let newSocial = `
    <div class="single-social-item flex flex-col justify-between items-center bg-gray-900 p-2 gap-4 flex-1">
      <div class="single-social-container relative flex flex-col gap-3 justify-center items-start p-2 self-start">
          <div class="flex flex-col md:flex-row gap-2">
            <label for="social-icon" class="min-w-[120px] max-w-[120px] overflow-hidden">Bootstrap Icon
            <a href="https://icons.getbootstrap.com/" target="_blank" class="text-base mx-2" style="color: #01fef3;">?</a>
            </label>
            <input class="social-icon min-w-0" type="text" value='' placeholder="bi bi-facebook" />
          </div>
          <div class="flex flex-col md:flex-row gap-2">
            <label for="social-title" class="min-w-[120px] max-w-[120px] overflow-hidden">Title</label>
            <input class="social-title min-w-0" type="text" value='' placeholder="Facebook" />
          </div>


          <div class="flex flex-col md:flex-row gap-2">
            <label for="social-color" class="min-w-[120px] max-w-[120px] overflow-hidden">Color</label>
            <input class="social-color min-w-0" type="text" value='#ffffff' />
          </div>

          <div class="flex flex-col md:flex-row gap-2">
            <label for="social-link" class="min-w-[120px] max-w-[120px] overflow-hidden">Link</label>
            <input class="social-link min-w-0" type="text" value='' placeholder="https://facebook.com" />
          </div>
      </div>
      <div class="remove-social cursor-pointer flex justify-center items-center gap-2 bg-red-700 px-3 py-2 rounded">
        <i class="bi bi-x-circle-fill rounded text-xs text-gray-200 hover:text-gray-400 cursor-pointer"></i>
        <span class="text-sm text-gray-400 font-semibold">Remove</span>
      </div>
    </div>`;

    $("#single-social-container-wrapper").append(newSocial);

    initSocialColorPicker();
  });

  function initSocialColorPicker() {
    $(".social-color").each(function () {
      $(this).wpColorPicker({
        change: function (event, ui) {
          var selectedColor = ui.color.toString();
          $(this).closest(".single-social-container").find(".color-preview-box").css("background-color", selectedColor);
        },
      });
    });
  }

  initSocialColorPicker();

  // Handle Remove Social Entry
  $(document).on("click", ".remove-social", function () {
    $(this).closest(".single-social-item").remove();
  });

  // Handle Save button click
  $("#save-theme-settings").on("click", function () {
    let settings = gatherAllSettings();

    // Disable the button to prevent multiple clicks
    $(this).prop("disabled", true);

    $.ajax({
      url: ajaxurl, // WordPress AJAX URL
      type: "POST",
      data: {
        action: "fp_save_theme_settings",
        settings: settings,
        nonce: fp_Data.save_nonce,
      },
      success: function (response) {
        if (response.success) {
          alert("Settings saved successfully!");
        } else {
          alert("There was an error saving the settings.");
        }
        // Re-enable the button
        $("#save-theme-settings").prop("disabled", false);
      },
      error: function () {
        alert("AJAX request failed.");
        // Re-enable the button
        $("#save-theme-settings").prop("disabled", false);
      },
    });
  });

  // ---------------------------------------------------- //
  // ----------------- Homepage Options ----------------- //
  // ---------------------------------------------------- //

  $("#homepage-items").sortable({
    handle: ".bi-arrows-move",
  });

  function handleHpTypeChange(hpTypeVal) {
    if (hpTypeVal === "taxonomy") {
      $("#hp-taxonomy-section, #h-heading-view, #h-show-ratings, #h-show-quality").removeClass("hidden").css("display", "flex");
      // Fetch taxonomy data
      getTaxonomyData();
    } else if (hpTypeVal === "featured") {
      $("#hp-taxonomy-section, #h-heading-view, #h-show-ratings, #h-show-quality").addClass("hidden").css("display", "none");
      setDefaultContentType();
    } else {
      $("#hp-taxonomy-section").addClass("hidden").css("display", "none");
      $("#h-heading-view, #h-show-ratings, #h-show-quality").removeClass("hidden").css("display", "flex");
      setDefaultContentType();
    }
  }

  // Call the function on page load with the initial value
  handleHpTypeChange($("#hp-type").val());

  // Attach the event handler to the change event
  $("#hp-type").on("change", function () {
    handleHpTypeChange($(this).val());
  });

  // Handle Add to List functionality
  $("#add-homepage-item").on("click", function () {
    // Gather data from the fields
    let item = {
      type: $("#hp-type").val(),
      content_type: $("#hp-content-type").val(),
      heading: $("#hp-heading").val(),
      limit: $("#hp-limit").val(),
      title_background: $("#hp-title-bg-effect").val(),
      image_source: $("#hp-image-source").val(),
      taxonomy: $("#hp-taxonomy").val(),
      image_size: $("#hp-image-size").val() || "original",
      show_ratings: $("#hp-show-ratings").is(":checked") ? 1 : 0,
      show_quality: $("#hp-show-quality").is(":checked") ? 1 : 0,
    };

    // Validate the data
    if (!item.type || !item.content_type || !item.limit || !item.title_background || !item.image_source) {
      alert("Please ReCheck All Fields.");
      return;
    }

    if (item.type === "meta" && !item.heading) {
      alert("Please enter a heading.");
      return;
    }

    if (item.type === "taxonomy" && !item.taxonomy) {
      alert("Please select a taxonomy.");
      return;
    }

    let isTaxonomyOrMeta = item.type === "taxonomy" || item.type === "meta";

    // Create a new list item for the sortable list
    let listItem = `<div class="sortable-item bg-gray-800 p-3 mb-2 rounded">
                        <i class="bi bi-arrows-move"></i>
                        <li>
                            <div class="i_type"><strong>Type:</strong> ${item.type}</div>
                            <div class="i_content_type"><strong>Content Type:</strong> ${item.content_type}</div>
                            <div class="i_limit"><strong>Limit:</strong> ${item.limit}</div>
                            <div class="i_title_background"><strong>Title Background Effect:</strong> ${item.title_background}</div>
                            <div class="i_image_source"><strong>Image Source:</strong> ${item.image_source}</div>
                            <div class="i_image_size"><strong>Image Size:</strong> ${item.image_size}</div>
                            
                            ${item.type === "taxonomy" ? `<div class="i_taxonomy"><strong>Taxonomy:</strong> ${item.taxonomy}</div>` : ""}
                            ${isTaxonomyOrMeta ? `<div class="i_heading"><strong>Heading:</strong> ${item.heading}</div>` : ""}
                            ${
                              isTaxonomyOrMeta
                                ? `<div class="i_show_ratings"><strong>Show Ratings:</strong> ${item.show_ratings ? "Yes" : "No"}</div>`
                                : ""
                            }
                            ${
                              isTaxonomyOrMeta
                                ? `<div class="i_show_quality"><strong>Show Quality:</strong> ${item.show_quality ? "Yes" : "No"}</div>`
                                : ""
                            }
                        </li>
                        <button class="remove-item remove-btn-base">Remove</button>
                    </div>`;

    // Append the new item to the list
    $("#homepage-items").append(listItem);

    // Clear the fields for new entry
    // $("#homepage-options input[type=text], #homepage-options input[type=number]").val("");
    // $("#homepage-options select").val("");
    // $("#hp-taxonomy-section").addClass("hidden");
  });

  // Make the list sortable using jQuery UI

  // Handle removing an item
  $(document).on("click", ".remove-item", function () {
    // console.log("Remove item clicked");
    $(this).closest(".sortable-item").remove();
  });

  $("#hp-taxonomy").on("change", function () {
    getTaxonomyData();
  });

  function getTaxonomyData() {
    let selectedTaxonomy = $("#hp-taxonomy").val();

    // console.log(selectedTaxonomy);

    if (!selectedTaxonomy) {
      return;
    }
    $.ajax({
      url: fp_Data.ajaxurl,
      type: "POST",
      data: {
        action: "fp_get_taxonomy_data",
        nonce: fp_Data.tax_nonce,
        taxonomy: selectedTaxonomy,
      },
      success: function (response) {
        if (!response.data) return;
        setTaxonomyData(response.data);
      },
      error: function () {
        alert("Failed to fetch content types.");
      },
    });
  }

  function setTaxonomyData($data) {
    let contentTypeSelect = $("#hp-content-type");

    // Clear existing options
    contentTypeSelect.empty();

    // Populate new options
    $.each($data, function (index, value) {
      contentTypeSelect.append(new Option(value.name, value.slug));
    });
  }

  // Set to default content type when Type is not Taxonomy
  function setDefaultContentType() {
    let contentTypeSelect = $("#hp-content-type");
    contentTypeSelect.empty();
    $.each(defaultContentType, function (index, value) {
      contentTypeSelect.append(new Option(value.name, value.slug));
    });
  }

  function gatherCustomizeData() {
    let customizeData = [];
    $("#homepage-items li").each(function () {
      let currentType = $(this).find(".i_type").text().replace("Type: ", "");
      let item = {
        type: currentType,
        content_type: $(this).find(".i_content_type").text().replace("Content Type: ", ""),
        limit: $(this).find(".i_limit").text().replace("Limit: ", "") || "10",
        title_background: $(this).find(".i_title_background").text().replace("Title Background Effect: ", ""),
        image_source: $(this).find(".i_image_source").text().replace("Image Source: ", ""),
        // heading: $(this).find(".i_heading").text().replace("Heading: ", ""),
        // taxonomy: $(this).find(".i_taxonomy").text().replace("Taxonomy: ", ""),
        image_size: $(this).find(".i_image_size").text().replace("Image Size: ", ""),
        // show_ratings: $(this).find(".i_show_ratings").text().replace("Show Ratings: ", "") === "Yes",
        // show_quality: $(this).find(".i_show_quality").text().replace("Show Quality: ", "") === "Yes",
      };

      if (currentType === "taxonomy") {
        item.taxonomy = $(this).find(".i_taxonomy").text().replace("Taxonomy: ", "");
      }

      if (currentType !== "featured") {
        item.heading = $(this).find(".i_heading").text().replace("Heading: ", "");
        item.show_ratings = $(this).find(".i_show_ratings").text().replace("Show Ratings: ", "") === "Yes";
        item.show_quality = $(this).find(".i_show_quality").text().replace("Show Quality: ", "") === "Yes";
      }

      // if item.limit is not a number, then alert the user and return
      if (isNaN(item.limit)) {
        alert("Limit must be a number.");
        return;
      }

      customizeData.push(item);
    });
    return customizeData;
  }

  $("#singlepage-items").sortable({
    handle: ".bi-arrows-move",
  });

  let currentSingleShortcode = $("#sp-shortcode").val();
  if (currentSingleShortcode === "fp-universal-view") {
    $("#universal-content-type-wrapper").removeClass("hidden").css("display", "flex");
    $("#universal-view-textarea").removeClass("hidden").css("display", "flex");
  }

  // Show/Hide textarea for fp-universal-view
  $("#sp-shortcode").on("change", function () {
    if ($(this).val() === "fp-universal-view") {
      $("#universal-content-type-wrapper").removeClass("hidden").css("display", "flex");
      $("#universal-view-textarea").removeClass("hidden").css("display", "flex");
    } else {
      $("#universal-content-type-wrapper").addClass("hidden").css("display", "none");
      $("#universal-view-textarea").addClass("hidden").css("display", "none");
    }
  });

  // Handle Add to List functionality
  $("#add-singlepage-item").on("click", function () {
    let shortcode = $("#sp-shortcode").val();
    // let content = shortcode === "fp-universal-view" ? $("#universal-content").val() : "";
    if (shortcode === "fp-universal-view") {
      var u_content = $("#universal-content").val();
      var u_content_type = $("#universal-content-type").val();

      console.log(u_content_type + " : " + u_content);

      //   alert(content_type + " : " + content);

      if (!u_content || !u_content_type) {
        alert("Please enter content and select content type.");
        return;
      }
      var u_content = escapeHtml(u_content);
    }

    let alreadyAdded = false;

    // Check if shortcode can only be added once
    if (shortcode !== "fp-universal-view") {
      $("#singlepage-items .i_shortcode").each(function () {
        if ($(this).text().includes(shortcode)) {
          alreadyAdded = true;
        }
      });
    }

    if (alreadyAdded) {
      alert("Already Added. This shortcode can only be added once.");
      return;
    }

    // Create a new list item for the sortable list
    let listItem = `
        <div class="sortable-item bg-gray-800 rounded flex justify-start items-center px-3 py-2 mb-3">
            <i class="bi bi-arrows-move"></i>
            <li id="single-shortcode-list" class="flex-1 flex gap-5">
                <div class="i_shortcode"><span class="inline-block min-w-24 me-1 font-semibold">Shortcode: </span>${shortcode}</div>
                ${
                  shortcode === "fp-universal-view"
                    ? `<div class="i_content_type"><span class="inline-block min-w-24 me-1 font-semibold">Type: </span>${u_content_type}</div>`
                    : ""
                }
                ${
                  shortcode === "fp-universal-view"
                    ? `<div class="i_content"><span class="inline-block min-w-24 me-1 font-semibold">Content: </span>${u_content}</div>`
                    : ""
                }
            </li>
            <button class="remove-item mt-2 px-3 py-2 bg-red-600 text-white font-semibold">Remove</button>
        </div>`;

    // Append the new item to the list
    $("#singlepage-items").append(listItem);

    // Clear the fields for new entry
    $("#universal-content").val("");
    // $("#sp-shortcode").val('');
    $("#universal-view-textarea").addClass("hidden");
  });

  $(document).on("click", ".remove-item", function () {
    $(this).closest(".sortable-item").remove();
  });

  function escapeHtml(unsafe) {
    return unsafe.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
  }

  function utf8ToBase64(str) {
    // Encode the string as an array of bytes (UTF-8), then to Base64
    const utf8Bytes = new TextEncoder().encode(str);
    const base64String = btoa(String.fromCharCode.apply(null, utf8Bytes));
    return base64String;
  }

  function gatherSinglePageData() {
    let singlePageData = [];
    $("#singlepage-items li").each(function () {
      let shortcode = $(this).find(".i_shortcode").text().replace("Shortcode: ", "");
      let item = {
        shortcode: shortcode,
      };

      if (shortcode === "fp-universal-view") {
        item.content_type = $(this).find(".i_content_type").text().replace("Type: ", "");
        // let e_base64 = $(this).find(".i_content").text().replace("Content: ", "");
        let e_base64 = $(this).find(".i_content").html().replace(`<span class="inline-block min-w-24 me-1 font-semibold">Content: </span>`, "");
        e_base64 = e_base64.replace(/<img[^>]*alt="([^"]*)"[^>]*>/g, "$1");
        console.log("ENCODED: ", e_base64);
        e_base64 = utf8ToBase64(e_base64);
        item.content = e_base64;
      }

      singlePageData.push(item);
    });

    return singlePageData;
  }


  function gatherOtherSettings() {
  }
});
