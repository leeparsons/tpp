jQuery(function(e){e("#mentor_form").on("submit",function(t){var n=[];e("#mentor_name").val().replace(/\s+/g,"")==""&&n.push("Please enter a mentor name");e("#mentor_country").val()==""&&n.push("Please select the mentor's country");e(".preview").find("img").length===0&&n.push("Please upload an image.");var r,i,s;r=document.getElementById("specialism_one").value.replace(/\s+/g,"");i=document.getElementById("specialism_two").value.replace(/\s+/g,"");s=document.getElementById("specialism_three").value.replace(/\s+/g,"");r==""&&i==""&&s==""&&n.push("Please enter a specialism");if(n.length>0){t.preventDefault();if(e("#message").length==0){var o=e('<div id="message"></div>');e(".page-article-part").find("form").eq(0).prepend(o)}else var o=e("#message");for(var u in n)o.append('<p class="wp-error">'+n[u]+"</p>");overlay.setHeader("Oops, sorry there were errors");o=[];for(var u in n)o.push(n[u]);overlay.setBody("Please see the error messages and fill in the required fields:<br><br>"+o.join("<br>"));overlay.populateInner();return!1}})});