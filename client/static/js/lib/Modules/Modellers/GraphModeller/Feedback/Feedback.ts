/// <reference path='./index.ts'/>

/// <reference path='../../../../../vendor/Definitions/Hashset.d.ts'/>
/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>

class Feedback
{
    public GeneralItems : IHashSet<FeedbackCode>;
    public SpecificItems : IHashtable<number, FeedbackRecord>;

    public constructor()
    {
        this.SpecificItems = new Hashtable();
        this.GeneralItems = new HashSet();
    }

    public Contains(id : number) {
        return this.SpecificItems.containsKey(id);
    }

    public AddFeedback(code : FeedbackCode, id ?: number)
    {
        if (id!= null) {
            if(!this.SpecificItems.containsKey(id)) {
                this.SpecificItems.put(id, new FeedbackRecord());
            }
            this.SpecificItems.get(id).AddCode(code);
        }
        else {
            this.GeneralItems.add(code);
        }
    }

    public GetFeedback(id : number)
    {
        if(this.SpecificItems.containsKey(id)) {
            return this.SpecificItems.get(id);
        }
    }
    
    public ClearFeedback(id ?: number)
    {
        if (id != null) {
            if(this.SpecificItems.containsKey(id)) {
                this.SpecificItems.get(id).ClearCodes();
            }
        }
        else {
            this.GeneralItems.clear();
            this.SpecificItems.clear();
        }
    }
    
    public static JsonToFeedback(json : string)
    {
        let f = new Feedback();
        let j = JSON.parse(json);

        let keys = Object.keys(j);
        for(let i = 0; i < keys.length; i++)
        {
            if(keys[i] == "general") {
                for(let k = 0; k < j["general"].length; k++) {
                    f.AddFeedback(j["general"][k]);
                }
            }
            else if(keys[i] == "specific") {
                let indices = Object.keys(j["specific"]);
                for(let k = 0; k < indices.length; k++) {
                    let id = indices[k];
                    let codes : FeedbackCode[] = j["specific"][id];
                    for (let h = 0; h < codes.length; h++) {
                        f.AddFeedback(codes[h], Number(id));
                    }
                }
            }
        }
        return f;
    }

    public isEmpty()
    {
        return this.SpecificItems.isEmpty() && this.GeneralItems.isEmpty();
    }

    public print()
    {
        let general = this.GeneralItems.values();
        console.log("general:");
        let s = "";
        for(let i = 0; i < general.length; i++) {
            s += general[i];
        }
        console.log(s);
        let specific = this.SpecificItems.keys();
        console.log("specific:");
        for(let i = 0; i < specific.length; i++) {
            let k = this.SpecificItems.get(specific[i]);
            console.log(specific[i], k.Items.values());
        }
    }
}
// type Feedback = IHashtable<number, FeedbackItem>