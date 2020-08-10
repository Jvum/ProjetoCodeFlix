import * as  React from 'react';
import Page from '../../components/Page';
import {Box, Fab} from '@material-ui/core';
import {Link} from "react-router-dom";
import AddIcon from '@material-ui/icons/Add'
import Table from '../category/Table';

export const  PageList = () => {
    return (
        <Page title={'Listar generos'}>
            <Box dir={'rtl'}>
                <Fab
                    title="Adicionar Gênero"
                    size="small"
                    component={Link}
                    to="/genres/create">
                    <AddIcon/>
                </Fab>
            </Box>
            <Box>
                <Table/>
            </Box>
        </Page>
    )
}

export default PageList;
