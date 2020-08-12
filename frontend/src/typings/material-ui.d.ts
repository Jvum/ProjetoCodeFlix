import {ComponentNameToClassKey} from '@material-ui/core/styles/overrides';

declare module '@material-ui/core/styles/overrides'{
    interface ComponentNameToClassKey {
        MuiDataTable: any;
        MUIDataTableToolbar: any;
        MUIDataTableHeadCell: any;
        MuiTableSorteLabel: any;
        MuiDataTableSelectCell: any;
        MUIDataTableBodyCell: any;
        MUIDataTableToolbarSelect: any;
        MUIDataTableBodyRow: any;
        MuiTablePagination: any;
    }
}