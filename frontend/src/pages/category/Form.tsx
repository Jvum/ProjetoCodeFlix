import * as React from 'react';
import {TextField, Checkbox, Box, Button, ButtonProps, makeStyles, Theme, FormControlLabel} from '@material-ui/core';
import {useForm} from 'react-hook-form';
import categoryHttp from '../../util/http/category-http';
import * as yup from '../../util/vendor/yup';
import { useEffect, useState } from 'react';
import { yupResolver } from '@hookform/resolvers';
import { useParams, useHistory } from 'react-router-dom';
import {useSnackbar} from 'notistack'
const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

const validationSchema = yup.object().shape({
    name: yup.string().label("Nome").required().max(255)
});

export const Form = () => {

    const classes = useStyles();


    const {register, handleSubmit, getValues, errors, reset, watch, setValue} = useForm({
        defaultValues: {
            is_active: true
        },
        resolver: yupResolver(validationSchema)
    });

    const snackBar = useSnackbar();
    const history = useHistory();
    const {id} = useParams();
    const [category, setCategory] = useState<{id: string} | null>(null);
    const [loading, setloading] = useState<boolean>(false);


    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        size: "medium",
        disabled: loading
    };

    useEffect(() => {
        snackBar.enqueueSnackbar('', {
            variant: 'success'
        })
    }, []);
     
    useEffect(() => {
        register({name: "is_active"})
    }, [register])

    useEffect(() => {
        if(!id){
            return;
        }
        setloading(true);
        categoryHttp.get(id).then(({data}) => {setCategory(data.data); reset(data.data)}).finally(() => setloading(false))

    }, []);

    function onSubmit(formData: any) {
        setloading(true);
        const http = !category ? categoryHttp.create(formData).finally(() => setloading(false)) 
        : categoryHttp.update(category.id, formData).finally(() => setloading(false));

        http.then(({data}) => {
            snackBar.enqueueSnackbar(
                'Categoria salva com sucesso',
                {variant: 'success'}
            )
            setTimeout(() => {
                event ? (
                    category ? history.replace(`/categories/${data.data.id}/edit`)
                    : history.push(`/categories/${data.data.id}/edit`)
                )
                : history.push('/categories')
            })
        })
        .catch((error) => {
            snackBar.enqueueSnackbar(
                'Não foi possível salvar a categoria',
                {variant: 'error'}
            )
        })
        .finally(() => setloading(false));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
            <TextField
                inputRef={register()}
                name="name"
                label="Nome"
                fullWidth
                variant={"outlined"}
                disabled={loading}
                error={errors[name] !== undefined}
                helperText={errors[name] && (errors[name] as any).message}
                InputLabelProps={{
                    shrink: true
                }}
                />
            <TextField
                inputRef={register}
                name="description"
                label="Descrição"
                multiline
                rows="4"
                disabled={loading}
                fullWidth
                variant={"outlined"}
                margin={"normal"}
                />
            <FormControlLabel
                control={
                    <Checkbox
                    color={"primary"}
                    name="is_active"
                    onChange={() => setValue("is_active", !getValues()['is_active'])}
                    checked={watch("is_active")}
                />
                }
                label={" Ativo?"}
                labelPlacement={"end"}
                disabled={loading}
                />
           
           
            <Box dir={"rtl"}>
                <Button {...buttonProps} color={"primary"} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button {...buttonProps} color={"secondary"} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    )
}