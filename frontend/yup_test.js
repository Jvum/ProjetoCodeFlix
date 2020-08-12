import * as yup from 'yup'
import { isValid } from 'date-fns';

const schema = yup.object().shape({
    name: yup.string().required(),
});

schema.isValid({ name: ''}).then(isValid => console.log(isValid));