import * as React from 'react';
import {TextField, Checkbox, Box, Button, ButtonProps, makeStyles, Theme, MenuItem} from '@material-ui/core';
import {useForm} from 'react-hook-form';
import categoryHttp from '../../util/http/category-http';
import { watch } from 'fs';
import { useEffect } from 'react';

const useStyles = makeStyles((theme: Theme) => {
    return {
        root: {
            
        },
        submit: {
            margin: theme.spacing(1)
        }
    }
})

export const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "outlined",
        size: "medium"
    };

    const [categories, setCategories] = React.useState<any[]>([]);
    const {register, handleSubmit, getValues, setValue, watch} = useForm({
        defaultValues: {
            categories_id: []
        }
    });
    const category = getValues()['categories_id'];

    useEffect(() => {
        register({name: 'categories_id'})
    }, [register]);

    useEffect(() => {
        categoryHttp
        .list()
        .then(response => setCategories(response.data.data))
    }, []);
    function onSubmit(formData: any) {
        categoryHttp
            .create(formData)
            .then((response) => console.log(response));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                inputRef={register}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                />
            <TextField
                select
                inputRef={register}
                name="categories_id"
                value={watch('categories_id')}
                label="Categories"
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                onChange={(e) => {
                    setValue('categories_id');
                }}
                SelectProps={{
                    multiple: true
                }}
            >
                <MenuItem value="">
                    <em>Selecione categories</em>
                </MenuItem>
                {
                    categories.map((category, key) => (
                        <MenuItem key={key} value={category.id}>
                            {category.name}
                        </MenuItem>
                    ))
                }
            </TextField>
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    )
}