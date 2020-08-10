import React, { useEffect, useState } from 'react';
import { MUIDataTable, MUIDataTableColumn } from 'mui-datatables';
import httpVideo from '../../util/http';
import format from "date-fns/format";
import parseISO from "date-fns/parseISO";
import categoryHttp from '../../util/http/category-http';

const CastMemberTypeMap = {
    1: 'Diretor',
    2: 'Ator'
}

const columnsDefinition: MUIDataTableColumn[] = [
    {
        name: "name",
        label: "Nome"
    },
    {
        name: "type",
        label: "Tipo",
        options: {
            customBodyRender(value, tableMeta, updateValue){
                return CastMemberTypeMap[value];
            }
        }
    },
    {
        name: "created_at",
        label: "Criado em",
        options: {
            customBodyRender(value, tableMeta, updateValue){
                return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>;
            }
        }
    }
];

type Props = {

};

const data = [
    {name: "teste1", "is_active": true, created_at: "2020-01-01"}
]

const Table = (props: Props) => {

    const [data, setData] = useState([]);
    useEffect(() => {
        categoryHttp
        .list()
        .then(({data}) => setData(data.data));
        
    }, []);

    return (
        <div>
            <MUIDataTable 
            title="Listagem de categorias"
            columns={columnsDefinition}
            data={data}
            />
        </div>
    )
}

export default Table;