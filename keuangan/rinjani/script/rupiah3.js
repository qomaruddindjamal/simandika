class Rupiah
{
    static RemoveLeadingZero(number)
    {
        if (number.length > 1)
        {
            while('' + number.charAt(0) === '0')
            {
                number = number.substring(1, number.length);
            }

            if (number.length === 0)
                number = "0";
        }
        return number;
    }

    static IsNumber(input)
    {
        return !isNaN(Number(input));
    }

    static NumberToRupiah(number)
    {
        let original = number;
        number = $.trim(number);

        if (number.length === 0)
            return number;

        let positif = true;
        if (number.charAt(0) === '-')
        {
            positif = false;
            number = number.substring(1, number.length);
            number = $.trim(number);
        }

        number = Rupiah.RemoveLeadingZero(number);

        if (!Rupiah.IsNumber(number))
            return original;

        let result = "";
        if (number.length < 4)
        {
            result = "Rp " + number;
        }
        else
        {
            let count = 0;
            for(let i = number.length - 1; i >= 0; i--)
            {
                result = number.charAt(i) + result;
                count++;

                if ((count === 3) && (i > 0))
                {
                    result = '.' + result;
                    count = 0;
                }
            }

            result = "Rp " + result;
        }

        if (!positif)
            result = "(" + result + ")";

        return result;
    }

    static RupiahToNumber(rp)
    {
        rp = $.trim(rp);

        let result = '';
        let positif = true;
        let isvalid = true;
        if (rp.length > 0)
        {
            if (rp.charAt(0) === "(")
            {
                positif = false;
                rp = rp.substring(1, rp.length);
                rp = $.trim(rp);
            }

            for (let i = 0; isvalid && i < rp.length; i++)
            {
                let chr = rp.charAt(i);
                let asc = chr.charCodeAt(0);

                if (asc >= 48 && asc <= 57)
                {
                    result = result + chr;
                }
                else
                {
                    isvalid = (asc === 82 || asc === 114 || asc === 80 || asc === 112 || asc === 32 || asc === 46 || asc === 40 || asc === 41);
                }
            }
        }

        if (isvalid)
        {
            if (positif)
                return result;
            else
                return "-" + result;
        }
        else
        {
            return rp;
        }
    }

    static FormatRupiah(id)
    {
        let element = $("#" + id);
        if (element.length === 0)
            return;

        element.val( Rupiah.NumberToRupiah(element.val()) );
    }

    static UnformatRupiah(id)
    {
        let element = $("#" + id);
        if (element.length === 0)
            return;

        element.val( Rupiah.RupiahToNumber(element.val()) );
    }
}








