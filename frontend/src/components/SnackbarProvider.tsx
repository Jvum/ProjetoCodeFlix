import React from 'react';
import {SnackbarProvider as NotSnackProvider, SnackbarProviderProps} from 'notistack';
import { IconButton } from '@material-ui/core';
import CloseIcon from '@material-ui/icons/Close';

export const SnackbarProvider: React.FC<SnackbarProviderProps> = (props) => {
    let SnackBarProdiverRef: any;
    const defaultProps: SnackbarProviderProps = {
        children: props,
        autoHideDuration: 3000,
        maxSnack: 3,
        anchorOrigin: {
            horizontal: 'right',
            vertical: 'top'
        },
        ref: (el) => SnackBarProdiverRef = el,
        action: (key) => (
            <IconButton color={"inherit"} style={{fontSize: 20}}
                onClick={() => SnackBarProdiverRef.closeSnackbar(key)}>
                <CloseIcon/>
            </IconButton>
        )
    };

    const newProps = {...defaultProps, ...props};
    return (
        <NotSnackProvider {...newProps}>
            {props.children}
        </NotSnackProvider>
    )
}

export default SnackbarProvider;
