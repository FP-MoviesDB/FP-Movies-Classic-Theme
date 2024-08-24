jQuery(document).ready((function(e){const t=e('.head-main-nav input[type="text"]'),s=e("#search-pc-btn"),i=e("#header-filter-icon"),n=e("#search-s-icon"),a=e("#results-head"),c=e("#pc-search-result"),l=e("#mobile-menu-toggle"),o=e("#menu-mobile-primary-content"),d=e("#search-mobile-btn"),r=e("#mobile-search-container"),u=e("#adv-search-input");let m=null,p="",h=null,f=null,v=!1;const g=e("#secondary-menu-toggle"),x=e("#menu-secondary-content");function y(){"0px"===o.css("maxHeight")||""===o.css("maxHeight")?(o.css("visibility","hidden"),o.removeClass("hidden"),l.removeClass("bi-list").addClass("bi-x"),setTimeout((function(){o.css("maxHeight","500px"),o.css("visibility","visible")}),300)):(o.css("maxHeight","0"),l.removeClass("bi-x").addClass("bi-list"),setTimeout((function(){o.css("visibility","hidden"),o.addClass("hidden")}),300))}function b(){if(p.length<2)return;let e=fp_sData.home_url+"?s="+p;window.location.href=e}function C(t){const s={m:e("#m-results"),p:e("#p-results")}[t];if(p.length<2||p.length<2&&!v)return a.hasClass("hidden")||a.addClass("hidden"),v=!1,void s.html('<p style="text-align: center;"><i class="bi bi-info-circle"></i> Enter at least 2 characters.</p>');p.length<2&&v||p!==m&&(m=p,s.html('<div class="spinner-wrapper"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>'),clearTimeout(f),f=setTimeout((function(){e.ajax({url:fp_sData.ajaxurl,method:"POST",data:{action:"fp_perform_search",nonce:fp_sData.nonce,search:p},success:function(t){t.success?(a.removeClass("hidden"),function(t,s){void 0===s&&(s=e(".results")),s.empty(),t.length>0?(t.forEach((function(t){var i=e("<div>",{class:"search-result-item",css:{}}),n=e("<a>",{href:t.p_link,css:{display:"flex"}}),a=e("<div>",{class:"search-result-item-image",css:{maxWidth:"50px",aspectRatio:"2:3",overflow:"clip"}}).append(e("<img>",{src:t.thumb,alt:t.title})),c=e("<div>",{class:"search-result-item-details",css:{}}).append(e("<span>",{text:e("<div>").html(t.title).text(),class:"search-result-item-title"})),l=e("<div>",{class:"search-result-item-details-meta",css:{display:"flex",justifyContent:"flex-start",alignItems:"center",columnGap:"1rem",fontSize:"0.8rem",flexWrap:"wrap",fontWeight:600,paddingLeft:"0.3rem",color:"#fff"}});l.append(e("<span>",{text:"movie"===t.post_type?"Movie":"TV",css:{display:"flex",alignItems:"center"}}).prepend(e("<img>",{src:fp_sData.icon_film,alt:t.post_type,class:"search-result-item-meta-icon"})),e("<span>",{text:t.vote,css:{display:"flex",alignItems:"center"}}).prepend(e("<i>",{class:"bi bi-star"})),e("<span>",{text:t.r_date,css:{display:"flex",alignItems:"center"}}).prepend(e("<img>",{src:fp_sData.icon_calendar,alt:t.r_date,class:"search-result-item-meta-icon"}))),c.append(l),n.append(a).append(c),i.append(n),s.append(i)})),v=!0):(s.html("<p style='text-align: center;'><i class='bi bi-info-circle'></i> No results found.</p>"),v=!1)}(t.data.results,s)):(s.html("<p>No results found.</p>"),v=!1)},error:function(){s.html("<p>Error retrieving results.</p>"),v=!1}})}),1e3))}l.on("click",(function(){y()})),e(document).on("click",(function(t){e(t.target).closest("#menu-mobile-primary-content").length||"0px"===o.css("maxHeight")||y()})),r.find("input[type='text']").on("keyup",(function(t){p=e(this).val(),"Enter"!==t.key?(h&&clearTimeout(h),h=setTimeout((function(){C("m")}),500)):b()})),d.on("click",(function(e){e.preventDefault(),b()})),e(document).on("click",(function(t){e(t.target).closest("#mobile-search-container").length||"1"!==r.css("opacity")||function(){r.css("opacity","0");var e=document.getElementById("search-icon");e.classList.remove("bi-x"),e.classList.add("bi-search"),setTimeout((function(){r.addClass("hidden")}),500)}()})),t.on("focus",(function(){e(this).removeClass("w-32").addClass("w-72"),i.removeClass("hidden"),n.addClass("hidden"),c.removeClass("hidden")})),t.on("keyup",(function(t){p=e(this).val(),"Enter"!==t.key?(h&&clearTimeout(h),h=setTimeout((function(){C("p")}),500)):b()})),t.on("blur",(function(){setTimeout((()=>{e(document.activeElement).is(t)||e(document.activeElement).is(i)||e(document.activeElement).is(s)||t.hasClass("w-72")&&(t.removeClass("w-72").addClass("w-32"),i.addClass("hidden"),n.removeClass("hidden"),c.addClass("hidden"))}),100)})),s.on("mousedown",(function(e){e.preventDefault(),b()})),i.on("mousedown",(function(e){e.preventDefault()})),e(document).on("keydown",(function(s){"s"!==s.key||e(document.activeElement).is('input, textarea, [contenteditable="true"]')||e(document.activeElement).is(t)||e(document.activeElement).is(u)||(s.preventDefault(),t.focus()),"Escape"===s.key&&e(document.activeElement).is(t)&&t.blur(),"Escape"===s.key&&"0px"!==x.css("maxHeight")&&(x.css("maxHeight","0"),setTimeout((function(){x.addClass("hidden")}),500))})),x.css({transition:"max-height 0.5s ease-in-out",maxHeight:"0"}),g.on("click",(function(){"0px"===x.css("maxHeight")||""===x.css("maxHeight")?(x.removeClass("hidden"),setTimeout((function(){x.css("maxHeight","450px")}),0)):(x.css("maxHeight","0"),setTimeout((function(){x.addClass("hidden")}),500))})),e("#header-menu-2 li:has(ul)").on("click",(function(t){t.stopPropagation(),t.preventDefault();const s=e(this).children("ul");e(t.target).closest("ul").is(s)||(s.is(":visible")?(s.slideUp(300),e(this).removeClass("active")):(s.slideDown(300),e(this).addClass("active")))})),e(document).on("click",(function(t){e(t.target).closest("#menu-secondary-main").length||"0px"===x.css("maxHeight")||(x.css("maxHeight","0"),setTimeout((function(){x.addClass("hidden")}),500))}));const k=e(".share_btn"),E=e(".share_toggle_button");k.on("click",(function(){E.toggleClass("active")})),e(document).on("click",(function(t){e(t.target).closest(".share_btn").length||E.removeClass("active")}))})),document.getElementById("search-icon").addEventListener("click",(function(e){e.stopPropagation();var t=document.getElementById("mobile-search-container"),s=t.querySelector("input"),i=document.getElementById("search-icon");t.classList.contains("hidden")?(t.classList.remove("hidden"),t.classList.add("flex"),i.classList.remove("bi-search"),i.classList.add("bi-x"),setTimeout((function(){t.style.opacity="1",s.focus()}),10)):(i.classList.remove("bi-x"),i.classList.add("bi-search"),t.style.opacity="0",setTimeout((function(){t.classList.remove("flex"),t.classList.add("hidden")}),500))}));