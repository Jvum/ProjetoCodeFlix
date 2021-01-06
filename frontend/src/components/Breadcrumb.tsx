/* eslint-disable no-nested-ternary */
import React, { useState } from "react";
import { makeStyles, Theme, createStyles } from "@material-ui/core/styles";
import List from "@material-ui/core/List";
import Link, { LinkProps } from "@material-ui/core/Link";
import { Container, Box } from "@material-ui/core";
import Typography from "@material-ui/core/Typography";
import MuitBreadcrumbs from "@material-ui/core/Breadcrumbs";
import { Route } from "react-router";
import { Link as RouterLink } from "react-router-dom";
import { Omit } from "@material-ui/types";
import { Location } from "history";
import routes from "../routes";
import RouteParser from "route-parser";

const breadcrumbNameMap: { [key: string]: string } = {};

routes.forEach(
  (route) => (breadcrumbNameMap[route.path as string] = route.label)
);

const useStyles = makeStyles((theme: Theme) =>
  createStyles({
    root: {
      display: "flex",
      flexDirection: "column",
    },
    linkRouter: {
      color: theme.palette.secondary.main,
      "&:focus, &:active": {
        color: theme.palette.secondary,
      },
      "&:hover": {
        color: theme.palette.secondary,
      },
    },
  })
);

interface LinkRouterProps extends LinkProps {
  to: string;
  replace?: boolean;
}

const LinkRouter = (props: LinkRouterProps) => (
  <Link {...props} component={RouterLink as any} />
);

export default function Breadcrumbs() {
  const classes = useStyles();

  function makeBreadcrumb(location: Location) {
    const pathnames = location.pathname.split("/").filter((x) => x);
    pathnames.unshift("/");
    return (
      <MuitBreadcrumbs aria-label="breadcrumb">
        {pathnames.map((value, index) => {
          const last = index === pathnames.length - 1;
          const to = `${pathnames
            .slice(0, index + 1)
            .join("/")
            .replace("//", "/")}`;
          const route = Object.keys(breadcrumbNameMap).find((path) =>
            new RouteParser(path).match(to)
          );

          if (route === undefined) {
            return false;
          }

          return last ? (
            <Typography color="textPrimary" key={to}>
              {breadcrumbNameMap[route]}
            </Typography>
          ) : (
            <LinkRouter
              color="inherit"
              to={to}
              key={to}
              className={classes.linkRouter}
            >
              {breadcrumbNameMap[route]}
            </LinkRouter>
          );
        })}
      </MuitBreadcrumbs>
    );
  }

  return (
    <Container>
      <Box paddingBottom={2}>
        <Route>
          {({ location }: { location: Location }) => makeBreadcrumb(location)}
        </Route>
      </Box>
    </Container>
  );
}