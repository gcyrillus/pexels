const imgFormPexels = document.querySelector("#search-form-pexels");
const qPexels =  document.querySelector("#search-pexels");
const nbPagePexels =  document.querySelector("#page-pexels");
const navpaginationPexels = document.querySelector("#searchPagination-pexels");
qPexels.addEventListener("change", function(){nbPagePexels.value='1'});
imgFormPexels.addEventListener("submit", fetchImagesPexels);
const limitPexels = 25;

function fetchImagesPexels(e) {
    
    e.preventDefault();
    const page = document.querySelector("#page-pexels").value;;
    const searchTerm = document.querySelector(".search-pexels").value;
    fetch(`https://api.pexels.com/v1/search?query=${searchTerm}&per_page=${limitPexels}&page=${page}`,
    {
      method: "GET",
      headers: {
        Accept: "application/json",
      Authorization: apikeyPexels
      }
    }
        
        
        
        ).
    
    then(response => {return response.json();}).
    then(resp => {
        console.log(resp);
        let maxPage = Math.ceil(resp.total_results / limitPexels);
        let pageNbr = page;
        let hitsArray = resp.photos;
        showImagesPexels(hitsArray);
        //if(maxPage > 20) maxPage =20;
        paginationPexels(maxPage,pageNbr);
        const alertMsg = document.querySelector(".alert-msg-pexels");
        if (hitsArray.length === 0) {
            alertMsg.innerHTML = " Essayer un autre mot";
            } else {
            alertMsg.innerHTML = "";
        }
        
        const text = document.querySelector(".search-text-pexels");
        text.innerHTML = `<h2>${searchTerm}: <small>page ${page} / ${maxPage}</small></h2>`; //${resp.total} trouvé(s).
        document.querySelector('#bottom-pexels').scrollIntoView();
        if (searchTerm && hitsArray.length === 0) {
            text.innerHTML = `
            <h1> ${searchTerm}:  ${hitsArray.length} </h1>
            `;
        }
        
    }).
    catch(err => console.log('err:' + err));
}



function showImagesPexels(hitsArray) {
    const results = document.querySelector(".results-pexels");
    //console.log(results);
    
    let output = '<div class="flex-images">';
    hitsArray.forEach(imgData => {
        output += `
        <div class="col-4 item" data-w="${imgData.width}"  data-h="${imgData.height}">
        <a href="${imgData.src.large}" class="pexelsUpload">upload</a>
        <img src="${imgData.src.large}"/>
        </div>
        `;
    });
    document.querySelector('.results-pexels').innerHTML = output;
    new setUploadLinksPexels();
    new flexImages({ selector: '.flex-images', rowHeight: 140 });
}

// affichage
function callpagePexels(position) {
    nbPagePexels.value = position;
    document.querySelector('#bottom-pexels').scrollIntoView();
}

// boutons précédent/suivant
function paginationPexels(totalPages, numpage) {
    let btnTag = "";
    let totalP = totalPages;
    // if page is greater than 1  then show the prev button
    // display prev button
    if (numpage > 1) {
        btnTag += `<button class="btn prev"onClick="callpagePexels(${numpage - 1})">&lsaquo;</button>`;
    }
    
    if (numpage < totalPages ) {
        btnTag += `<button class="btn next"onClick="callpagePexels(${++numpage})">&rsaquo;</button>`;
    }
    
    navpaginationPexels.innerHTML = btnTag;
}

// demande de fichier
function setUploadLinksPexels() {  
    let myLinks= document.querySelectorAll('a.pexelsUpload')
    for (let lks of myLinks) {
        lks.addEventListener('click',function(e){
            e.preventDefault(); 
            e.stopPropagation();
            uploadImagePexels('?url='+lks.href);
        }, false);
    }
    
}

// affichage fichier demandé et codes d'insertions
function dialogUploadedImagePexels(imgArray) {
    const succeedUpload = document.querySelector("#medias-table tbody tr:first-child td:first-child");
    // on garde juste le nom du fichier
    let file = imgArray[3].replace(/\.[^/.]+$/, "");
    console.log(file); // Output: "file"
    let done = `<div id="justUploaded-pexels">
    <dialog open>
    <h2>Image <i>${file}</i></h2>
    <div><b>Lien:</b><code>${imgArray[5]}</code><div data-copy="${imgArray[5]}" title="Copier le lien dans le presse-papier" class="ico">&#128203;
    <div>lien copié</div>
    </div></div>
    <div><b>HTML:</b> 
    <code>&lt;img src="${imgArray[5]}" alt="${file} uploaded@pixabay" &gt;</code>
    <div data-copy="<img src='${imgArray[5]}' alt='${imgArray[3]} uploaded@pexels'>" title="Copier le code dans le presse-papier" class="ico">&#128203;
    <div>code copié</div>
    </div> 
    
    </div>
    <div><b>Preview:</b> <img src="${imgArray[1]}${imgArray[3]}" alt="image prewiew"></div>
    <form method="dialog">
    <button type="button" onclick="this.closest('dialog').removeAttribute('open')">OK</button>
    </form>
    </dialog>
    </div>
    `;
    // insertion dans tbody pour beneficier de la fonction copy/paste native . affichage
    succeedUpload.insertAdjacentHTML('beforeend', done);
    
}

// telechargement depuis le serveur distant de pixabay
function uploadImagePexels(ziHref) {
    let protocol=window.location.protocol;
    let domain=window.location.host;
    let url = window.location.pathname;
    
    fetch(protocol+url+ziHref).
    then(response => {        
    return response.json();}).
    then(resp => {
        console.log(resp);
        dialogUploadedImagePexels(resp);
    }).
    catch(err => console.log('err:' + err));
    return
}

