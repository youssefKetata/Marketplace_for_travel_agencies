import React, {useState, useEffect} from 'react';
import DataTable from "react-data-table-component";
import DataTableExtensions from "react-data-table-component-extensions";
import "react-data-table-component-extensions/dist/index.css";
import "./styles.css";
// import 'bootstrap';
/*import 'bootstrap/dist/js/bootstrap.js';
import "bootstrap/dist/css/bootstrap.css";*/
import DropDownColumn from "./extraColumns";


function MyDataTable (props) {
    const URL_api = props.data_route;
    const action_route = props.action_route;
    const title = props.title;
    const selectableRows = (props.selectableRows === 'true');
    const dropdownBtn = (props.dropdownBtn === 'true');
    const expandOnRowClicked = (props.expandOnRowClicked === 'true');

    //1 - hooks
    const [data, setData] = useState( [] )
    const [columns, setColumns] = useState([])

    const tableData = {
        columns,
        data
    };

    //2 - Get data and columns from the specified URLS
    const bindData = async () => {
        const response = await fetch(URL_api) // change url_api de  http://dev2.3t.tn/admin/pays/filter
        const json_resp = await response.json()
        const json_data = json_resp['data'];

        setData(json_data);

        const json_columns = json_resp['columns'];

        console.log(json_data);
        for (var i = 0; i < json_columns.length; i++) {
            if(json_columns[i].state) {

                const selector  = json_columns[i].selector;
               // console.log('SELECTOR : ' + selector);
                const col_state =  {
                    width:'10',
                    name : json_columns[i].name+"2",
                    cell: row =>
                        (
                            //
                            <img
                                width={30} height={30}
                                src={window.location.origin+'/state_'+row[selector]+'.png'}
                            />
                            // json_columns[i].selector
                            //<div>{ row[selector] }</div>
                        )
                };
                json_columns.push(col_state)
            }
        }

        (dropdownBtn && json_columns.push(DropDownColumn(json_resp['actions'], action_route)));
        setColumns(json_columns)
        //console.log(json_columns);
    }
    console.log(columns) // to show colmuns in the navigator console
    console.log(data) // to show data in the navigator console
    useEffect( ()=>{ bindData()}, [])

    //console.log(columns);
    function getNumberOfPages(rowCount, rowsPerPage) {
        return Math.ceil(rowCount / rowsPerPage) ;
    }

    function toPages(pages) {
        const results = [];

        for (let i = 1; i <= pages; i++) {
            results.push(i);
        }

        return results;
    }

    // RDT exposes the following internal pagination properties
    const BootyPagination = ({
                                 rowsPerPage,
                                 rowCount,
                                 onChangePage,
                                 onChangeRowsPerPage, // available but not used here
                                 currentPage
                             }) => {
        const handleBackButtonClick = () => {
            onChangePage(currentPage - 1);
        };

        const handleNextButtonClick = () => {
            onChangePage(currentPage + 1);
        };

        const handlePageNumber = (e) => {
            onChangePage(Number(e.target.value));
        };

        const pages = getNumberOfPages(rowCount, rowsPerPage);
        const pageItems = toPages(pages);
        const nextDisabled = currentPage === pageItems.length;
        const previosDisabled = currentPage === 1;

        return (
            <nav>
                <ul className="pagination">
                    <li className="page-item">
                        <button
                            className="page-link"
                            onClick={handleBackButtonClick}
                            disabled={previosDisabled}
                            aria-disabled={previosDisabled}
                            aria-label="previous page"
                        >
                            Previous
                        </button>
                    </li>
                    {pageItems.map((page) => {
                        const className =
                            page === currentPage ? "page-item active" : "page-item";

                        return (
                            <li key={page} className={className}>
                                <button
                                    className="page-link"
                                    onClick={handlePageNumber}
                                    value={page}
                                >
                                    {page}
                                </button>
                            </li>
                        );
                    })}
                    <li className="page-item">
                        <button
                            className="page-link"
                            onClick={handleNextButtonClick}
                            disabled={nextDisabled}
                            aria-disabled={nextDisabled}
                            aria-label="next page"
                        >
                            Next
                        </button>
                    </li>
                </ul>
            </nav>
        );
    };


    const BootyCheckbox = React.forwardRef(({ onClick, ...rest }, ref) => (
        <div className="form-check">
            <input
                htmlFor="booty-check"
                type="checkbox"
                className="form-check-input"
                ref={ref}
                onClick={onClick}
                {...rest}
            />
            <label className="form-check-label" id="booty-check" />
        </div>
    ));


    //4 - Returning DataTable in <div>
    return (
        <div className="App">
            <div className="card">
                <DataTableExtensions {...tableData}>
                    <DataTable
                        title={title}
                        columns={columns}
                        data={data}
                        defaultSortFieldID={1}
                        pagination = {true}
                        paginationComponent={BootyPagination}
                        selectableRows = {selectableRows}
                        selectableRowsComponent={BootyCheckbox}
                        expandOnRowClicked = {expandOnRowClicked}
                        responsive={true}
                        highlightOnHover = {true}
                    />
                </DataTableExtensions>
            </div>
        </div>
    );
}
export default MyDataTable;