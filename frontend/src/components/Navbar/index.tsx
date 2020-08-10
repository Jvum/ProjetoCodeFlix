import * as React from 'react';
import {AppBar, Toolbar, Typography, Button, makeStyles, Theme, IconButton,  MenuItem} from "@material-ui/core";
import {Menu} from './Menu';

const useStyles = makeStyles((theme: Theme) => ({
    toolbar: {
        backgroundColor: '#000000'
    },
    title: {
        flexGrow: 1,
        textAlign: 'center'
    },
    logo: {
        width: 100,
        //Ative media query para o tamanho sm, mudando o width de acordo com o tamanho da tela
        [theme.breakpoints.up('sm')]: {
            width: 170
        }
    }
}))

export const Navbar: React.FC = () => {
    const classes = useStyles();

    const [anchorEl, setAnchorEl] = React.useState(null);
    const open = Boolean(anchorEl);

    const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
    const handleClose = () => setAnchorEl(null);

    return (
       <AppBar>
           <Toolbar className={classes.toolbar}>
                <Menu/>
               <Typography className={classes.title}>
                   <img src="" alt="CodeFlix"/>
               </Typography>
               <Button color="inherit">Login</Button>
           </Toolbar>
       </AppBar>
    );
};