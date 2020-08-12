import * as React from 'react';
import {TextField, Checkbox, Box, Button, ButtonProps, makeStyles, Theme, FormLabel, RadioGroup, FormControl, FormControlLabel, Radio} from '@material-ui/core';
import {useForm} from 'react-hook-form';
import categoryHttp from '../../util/http/category-http';
import { useEffect } from 'react';


const useStyles = makeStyles((theme: Theme) => {
    return {
        submit: {
            margin: theme.spacing(1)
        }
    }
})

export const Form = () => {

    const classes = useStyles();

    const buttonProps: ButtonProps = {
        className: classes.submit,
        variant: "contained",
        size: "medium",
        color: "secondary"
    };

    const {register, handleSubmit, getValues, setValue} = useForm();

    useEffect(() => {
        register({name: 'type'})
    }, [register]);

    function onSubmit(formData: any) {
        categoryHttp
            .create(formData)
            .then((response) => console.log(response));
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)}>
           <TextField
            name="name" 
            label="Nome" 
            fullWidth
            variant={"outlined"}
            inputRef={register}>
           </TextField>
           <FormControl margin={"normal"}>
            <FormLabel component="legend">
                Tipo
            </FormLabel>
            <RadioGroup
             name="type"
             onChange={(e) => {
                 setValue('type', parseInt(e.target.value));
             }}>
                <FormControlLabel value="1" control={<Radio color={"primary"}/>} label="Diretor"></FormControlLabel>
                <FormControlLabel value="2" control={<Radio color={"primary"}/>} label="Ator"></FormControlLabel>
            </RadioGroup>
           </FormControl>
            <Box dir={"rtl"}>
                <Button {...buttonProps} onClick={() => onSubmit(getValues())}>Salvar</Button>
                <Button {...buttonProps} type="submit">Salvar e continuar editando</Button>
            </Box>
        </form>
    )
}