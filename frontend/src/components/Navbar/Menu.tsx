import * as React from "react";
import {
  AppBar,
  Typography,
  Button,
  makeStyles,
  Theme,
  IconButton,
  Menu as MuiMenu,
  MenuItem,
} from "@material-ui/core";
import MenuIcon from "@material-ui/icons/Menu";
import routes, { MyRouteProps } from "../../routes";
import { Link } from "react-router-dom";
const useStyles = makeStyles((theme: Theme) => ({
  toolbar: {
    backgroundColor: "#000000",
  },
  title: {
    flexGrow: 1,
    textAlign: "center",
  },
  logo: {
    width: 100,
    //Ative media query para o tamanho sm, mudando o width de acordo com o tamanho da tela
    [theme.breakpoints.up("sm")]: {
      width: 170,
    },
  },
}));

export const Menu: React.FC = () => {
  const classes = useStyles();

  const listRoutes = ["dashboard", "categories.list", "cast_members.list", "genres.list"];
  const menuRoutes = routes.filter((route) => listRoutes.includes(route.name));
  const [anchorEl, setAnchorEl] = React.useState(null);
  const open = Boolean(anchorEl);

  const handleOpen = (event: any) => setAnchorEl(event.currentTarget);
  const handleClose = () => setAnchorEl(null);

  return (
    <React.Fragment>
      <IconButton
        edge="start"
        color="inherit"
        aria-label="open drawer"
        aria-controls="menu-appbar"
        aria-haspopup="true"
        onClick={handleOpen}
      >
        <MenuIcon />
      </IconButton>
      <MuiMenu
        open={open}
        anchorEl={anchorEl}
        anchorOrigin={{ vertical: "bottom", horizontal: "center" }}
        transformOrigin={{ vertical: "top", horizontal: "center" }}
        getContentAnchorEl={null}
        onClose={handleClose}
        id="menu-appbar"
      >
        {
        listRoutes.map(
            (routeName, key) => {
          const route = menuRoutes.find((route) => route.name === routeName) as MyRouteProps;
          return (
            <MenuItem key={key} component={Link} to={route.path as string} onClick={handleClose}>
                {route.label}
              </MenuItem>
          );   
        })}
      </MuiMenu>
      <Typography className={classes.title}>
        <img src="" alt="CodeFlix" />
      </Typography>
      <Button color="inherit">Login</Button>
    </React.Fragment>
  );
};
