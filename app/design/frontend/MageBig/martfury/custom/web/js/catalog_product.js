document.addEventListener("DOMContentLoaded", function () {
    const anchor = document.querySelector(".span-unique-class-detail");
    console.log(anchor);

    if (anchor) {
       
        const anchorText = [...anchor.childNodes]
        .filter(node => node.nodeType === Node.TEXT_NODE) 
        .map(node => node.nodeValue.trim()) 
        .join('');
        console.log("Anchor Text",anchorText);
       
        const span3 = anchor.querySelector("#span2");
        console.log("Span",span3)

        if (anchorText === "Details") {
            span3.style.display ="inline-block";
        } else {
            span3.style.display = "none"; 
        }
    }
});


document.addEventListener("DOMContentLoaded", function () {
    // Select all tab links
    const tabLinks = document.querySelectorAll(".description-heading");

    // Select all tab content sections
    const tabContents = document.querySelectorAll(".data.item.content");

    // Show the first tab by default
    if (tabLinks.length > 0 && tabContents.length > 0) {
        tabContents[0].style.display = "block"; // Show the first tab content
    }

    // Add click event listeners to each tab
    tabLinks.forEach(tab => {
        tab.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default anchor behavior

            // Get the target content ID from href attribute
            const targetId = this.getAttribute("href").substring(1);
            
            // Hide all content sections
            tabContents.forEach(content => {
                content.style.display = "none";
            });

            // Show the clicked tab's content
            document.getElementById(targetId).style.display = "block";
        });
    });
});


console.log("PRODUCT PAGE JS WORKING");


