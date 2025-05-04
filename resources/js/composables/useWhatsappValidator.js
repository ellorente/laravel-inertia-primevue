export function useWhatsappValidator() {
  function validateAndFormatWhatsapp(value) {
      const number = (value || '').replace(/\D/g, '');

      // Ya tiene el código de país y 12 dígitos
      if (number.startsWith('57') && number.length === 12) {
          return { valid: true, formatted: number };
      }

      // Tiene 10 dígitos y comienza por 3 (celular en Colombia)
      if (number.length === 10 && number.startsWith('3')) {
          return { valid: true, formatted: '57' + number };
      }

      return {
          valid: false,
          formatted: value,
          error: 'El número de WhatsApp no es válido',
      };
  }

  return { validateAndFormatWhatsapp };
}