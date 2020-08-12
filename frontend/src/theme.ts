import {createMuiTheme} from '@material-ui/core'
import red from '@material-ui/core/colors/red'
import { PaletteOptions, SimplePaletteColorOptions } from '@material-ui/core/styles/createPalette';
import { green } from '@material-ui/core/colors';

const palette: PaletteOptions = {
    primary: {
        main: '#79aec8',
        contrastText: '#fff'
    },
    secondary: {
        main: '#4db5ab',
        contrastText: '#fff'
    },
    background: {
        default: '#fafafa'
    },
    error: {
        main: red[400],
    },
    success: {
        main: green[500],
    }
};

const theme = createMuiTheme({
    palette,
    overrides: {
        MuiDataTable: {
            paper: {
                boxShadow: "none",
            }
        },
        MUIDataTableToolbar: {
            root: {
                minHeight: '50px',
                backgroundColor: palette.background?.default
            },
            icon: {
                color: (palette!.primary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &.focus': {
                    color: '#055a52',
                }
            },
            iconActive: {
                color: (palette!.primary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &.focus': {
                    color: '#055a52',
                }
            }
        },
        MUIDataTableHeadCell: {
            fixedHeader: {
                paddingTop: 7,
                paddingBottom: 7,
                backgroundColor: (palette!.primary as SimplePaletteColorOptions).main,
                color: '#ffffff',
                '&[aria-sort]': {
                    backgroundColor: '#459ac4',
                }
            },
            sortActive: {
                color: '#fff'
            },
            sortAction: {
                alignItems: 'center'
            },
            sortLabelRoot: {
                '& svg': {
                    color: '#fff !important',
                } 
            }
            
        },
        MuiDataTableSelectCell: {
            headerCell: {
                backgroundColor : (palette!.primary as SimplePaletteColorOptions).main,
                '& span': {
                    color: '#fff !important'
                }
            }
        },
        MUIDataTableBodyCell: {
            root: {
                color: (palette!.secondary as SimplePaletteColorOptions).main,
                '&:hover, &:active, &.focus' : {
                    color: (palette!.secondary as SimplePaletteColorOptions).main,
                }
            }
        },
        MUIDataTableToolbarSelect: {
            title: {
                color: (palette!.secondary as SimplePaletteColorOptions).main,
            },
            iconButton: {
                color: (palette!.secondary as SimplePaletteColorOptions).main
            }
        },
        MUIDataTableBodyRow: {
            root : {
                color: (palette!.secondary as SimplePaletteColorOptions).main,
                '&:nth-child(odd)': {
                    backgroundColor: palette!.background?.default,
                },
            }
        }
    }
});

export default theme;