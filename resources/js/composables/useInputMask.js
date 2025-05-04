export function useInputMask() {
    const applyMask = ({ value, maxLength = null, onlyNumbers = false, toUppercase = false, toLowercase = false , toCapitalize = false }) => {
        
        if (onlyNumbers) {
            value = value.replace(/\D/g, ''); // elimina todo lo que no sea número
        }

        if (toUppercase) {
            value = value.toUpperCase();
        }

        if (toLowercase) {
            value = value.toLowerCase();
        }

        if (toCapitalize) {
            value = value
                .toLowerCase()
                .split(" ")
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" ");
        }

        if (maxLength !== null && value.length > maxLength) {
            value = value.substring(0, maxLength);
        }

        return value;
    };

    const handleKeydown = ({ event, modelValue, maxLength = null, onlyNumbers = false }) => {
        const allowedKeys = [
            'Backspace', 'ArrowLeft', 'ArrowRight', 'Delete', 'Tab', 'Home', 'End'
        ];

        const isNumber = /^[0-9]$/.test(event.key);

        if (onlyNumbers && !isNumber && !allowedKeys.includes(event.key)) {
            event.preventDefault(); // ❌ Bloquea letras
        }

        if (maxLength !== null &&
            modelValue !== null &&
            modelValue !== undefined &&
            modelValue.length >= maxLength &&
            !allowedKeys.includes(event.key)) {
            event.preventDefault(); // ❌ Bloquea si ya alcanzó el máximo
        }
    };

    return {
        applyMask,
        handleKeydown
    };
}