import {LocaleObject, setLocale} from 'yup';
const ptBR: LocaleObject = {
    mixed: {
        required: '${path} é requerido'
    },
    string: {
        max: '${path} precisa ter no maximo ${max} caracteres'
    },
    number: {
        min: '${path} precisa ser no minimo ${min}'
    }
}

setLocale(ptBR);

export * from 'yup';