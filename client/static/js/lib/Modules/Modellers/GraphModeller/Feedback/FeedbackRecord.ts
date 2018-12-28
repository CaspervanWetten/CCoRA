/// <reference path='./index.ts'/>

/// <reference path='../../../../../vendor/Definitions/Hashset.d.ts'/>

class FeedbackRecord
{
    public Items : IHashSet<FeedbackCode>;

    public constructor()
    {
        this.Items = new HashSet();
    }

    public AddCode(code : FeedbackCode)
    {
        this.Items.add(code);
    }

    public ClearCodes()
    {
        this.Items.clear();
    }

    public IsEmpty()
    {
        let b = this.Items.isEmpty();
        return b;
    }

    public GetCodes()
    {
        return this.Items.values();
    }

    public ToString()
    {
        let strings = this.ToStrings();
        let result = strings.join(", ");

        return result;
    }

    public ToStrings() : string[]
    {
        let result = [];
        let codes = this.GetCodes();
        for(let i = 0; i < codes.length; i++) {
            result.push(FeedbackTranslator.Translate(codes[i]));
        }
        return result;
    }
}