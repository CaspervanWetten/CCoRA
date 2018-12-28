/// <reference path='../HTMLGenerators/FormBuilder/index.ts'/>

class FormFactory
{
    public static GetUserRegistrationForm()
    {
        let builder = new FormBuilder(false);
        builder.AddLabel("Name", "name");
        builder.AddInput("name", "text", "your username");

        return builder.Render();
    }

    public static GetPetrinetRegistrationForm()
    {
        let builder = new FormBuilder(false);
        builder.AddLabel("Petri net", "petrinet");
        builder.AddInput("petrinet", "file");

        return builder.Render();
    }
}