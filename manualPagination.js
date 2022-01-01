function pagination()
    {
        let row = 2;
        let pageNumber = '';
        let j = (parseInt(start) - row); // row = 2
        let printOnceAgain = true;
        let prev = '';
        let next = '';
        j = j <= 0 ? 0 : j;
        let printOnce = j+1 == 1 ? false : true;
        for(let i = j; i < totalPage ; i++) {
            if (i+1 != 1 && printOnce == true) {
                printOnce = false;
                pageNumber += `...`;
            }
            if(j < parseInt(start)+row) { //row = 2
             pageNumber +=
                     `
                    <li id="page-"${(parseInt(totalItemPerPage) * (i - 1)) + 1}>
                        <button class="h-10 px-5 ${ i == start ? "bg-green-500 text-white color_switch_bac" : "text-gray-600 bg-white border" } border border-r-0 border-gray-600 pagintion" data-start="${(parseInt(totalItemPerPage) * (i - 1)) + 1}">${i+1}</button>
                    </li>`
                 } else if (i+1 == totalPage && printOnceAgain == true) {
                printOnceAgain = false;
                pageNumber +=
                    `<li id="page-"${(parseInt(totalItemPerPage) * (i - 1)) + 1}>
                        <button class="h-10 px-5 ${ i == start ? "bg-green-500 text-white color_switch_bac" : "text-gray-600 bg-white border" } border border-r-0 border-gray-600 pagintion" data-start="${(parseInt(totalItemPerPage) * (i - 1)) + 1}">${i+1}</button>
                    </li>`
            } else {
                pageNumber += `...`;
            }
            j++;
        }

        if (parseInt(start) - 1 >= 0) {
             prev = `<li>
                        <button class="h-10 px-5 text-gray-600 bg-white border border-r-0 border-gray-600 hover:bg-gray-100 page-prev">${jsLang('Prev')}</button>
                    </li>`;
           }
        if (parseInt(start) + 1 != totalPage) {
            next = `<li>
                        <button class="h-10 px-5 text-gray-600 bg-white border border-gray-600 hover:bg-gray-100 page-next">${jsLang('Next')}</button>
                    </li>`;
        }

        let pageHtml = `
                   <div id="pagination" data-page = ${totalItemPerPage}>
                        <ul class="flex">
                            ${prev}
                            ${pageNumber}
                            ${next}
                        </ul>
                   </div>
                        `;
        $('#pagination').replaceWith(pageHtml);
    }