function pagination(currPage = 1)
    {
        let pageGap = 2;
        let prev = '';
        let next = '';
        let pageNumber = '';
        if (totalPage > 1) {
            let middleDot = true;
            let firstDot = true;
            let preFlag = true;
            let currentPage = currPage;
            let gapChange = pageGap;
            let gapId = 1;
            let dotEnable = true;
            for (let i = 0; i < totalPage; i++) {
                let startNextPage = (parseInt(totalItemPerPage) * (currentPage - 1));

                if (currPage != 1 && currPage - gapChange > 1 && firstDot == true) {
                    firstDot = false;
                    pageNumber += `...`;
                }

                if (currPage != 1 && preFlag == true) {
                    for (let k = gapChange; k >= 1; k--) {
                        preFlag = false;
                        let preCurrentPage = currentPage - k;
                        if (preCurrentPage > 0) {
                            let startNextPagePre = (parseInt(totalItemPerPage) * (preCurrentPage - 1));
                            pageNumber +=
                                `
                            <li id="page-"${startNextPagePre}>
                                <button class="h-10 px-5 ${parseInt(startNextPagePre) == start ? "bg-green-500 text-white color_switch_bac" : "text-gray-600 bg-white border"} border border-r-0 border-gray-600 pagintion" data-start="${startNextPagePre}" data-pageNumber="${preCurrentPage}">${preCurrentPage}</button>
                            </li>`;
                        }
                    }
                }

                if (currentPage <= totalPage-1 && gapId <= gapChange) {
                    dotEnable = false;
                    pageNumber +=
                        `
                    <li id="page-"${startNextPage}>
                        <button class="h-10 px-5 ${parseInt(startNextPage) == start ? "bg-green-500 text-white color_switch_bac" : "text-gray-600 bg-white border"} border border-r-0 border-gray-600 pagintion" data-start="${startNextPage}" data-pageNumber="${currentPage}">${currentPage}</button>
                    </li>`;
                    gapId++;
                } else {
                    dotEnable = true;
                }


                if (dotEnable == true && middleDot == true && currentPage-1 != totalPage-1 && currentPage < totalPage ){
                    middleDot = false;
                    pageNumber += `...`;
                }
                if (currentPage == totalPage) {
                    dotEnable = false;
                    pageNumber +=
                        `
                    <li id="page-"${startNextPage}>
                        <button class="h-10 px-5 ${parseInt(startNextPage) == start ? "bg-green-500 text-white color_switch_bac" : "text-gray-600 bg-white border"} border border-r-0 border-gray-600 pagintion" data-start="${startNextPage}" data-pageNumber="${currentPage}">${currentPage}</button>
                    </li>`;
                }
                currentPage++;
            }



                if (parseInt(start) - 1 >= 0) {
                    prev = `<li>
                                <button class="h-10 px-5 text-gray-600 bg-white border border-r-0 border-gray-600 hover:bg-gray-100 page-prev">${jsLang('Prev')}</button>
                            </li>`;
                }
                if (currentPage != totalPage * 2) {
                    next = `<li>
                                <button class="h-10 px-5 text-gray-600 bg-white border border-gray-600 hover:bg-gray-100 page-next">${jsLang('Next')}</button>
                            </li>`;
                }

       }
        let pageHtml = `
                       <div id="pagination" data-page = ${totalPage}>
                            <ul class="flex">
                                ${prev}
                                ${pageNumber}
                                ${next}
                            </ul>
                       </div>
                            `;
        $('#pagination').replaceWith(pageHtml);
    }