
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

console.log("PRODUCT PAGE JS WORKING");


