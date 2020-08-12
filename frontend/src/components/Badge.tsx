import * as React from 'react';
import { Chip, createMuiTheme, MuiThemeProvider } from '@material-ui/core';
import theme from '../theme';

const yesTheme = createMuiTheme( {
    palette: {
        primary: theme.palette.success,
        secondary: theme.palette.secondary
    }
})

export const BadgeYes = () => {
    return (
        <MuiThemeProvider theme={yesTheme}>
            <Chip label="Sim" color="primary"/>
        </MuiThemeProvider>
        
    );
};

export const BadgeNo = () => {
    return (
        <MuiThemeProvider theme={yesTheme}>
            <Chip label="Sim" color="secondary"/>
        </MuiThemeProvider>
    );
};