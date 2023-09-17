export const isEmail = (value) => {
    const regex = /[^@ \t\r\n]+@[^@\t\r\n]+\.[^@\t\r\n]+/i;
    if (!regex.test(value)) {
      return 'Invalid email address';
    }

    // All is good
    return true;
}

export const isPhone = (value) => {
    const regex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,7}$/;
    if (!regex.test(value)) {
      return 'Invalid phone number';
    }

    // All is good
    return true;
}

export const isPassword = (value) => {

    // if the field is not a valid email
    const regex = /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/;
    if (!regex.test(value)) {
      return 'Password must be 8 charaters long, containing capital and small letters, a number and a special character such as #?!@$ %^&*-';
    }

    // All is good
    return true;
}

export const isRequired = (value) => {

    if (value){
        return true;
    }

    return 'This field is required';
}

export const isTrue = (value) => {
  
    if (value){ 
        return true;
    }

    return 'This field is required';
}