-- Run this ONLY if you already imported the first version of database.sql.
USE student_housing;
ALTER TABLE rooms
  ADD size_sqm SMALLINT NOT NULL DEFAULT 12 AFTER floor,
  ADD bed_type VARCHAR(80) NOT NULL DEFAULT 'Single bed' AFTER size_sqm,
  ADD look TEXT NOT NULL AFTER bed_type;
UPDATE rooms SET size_sqm=10, bed_type='Single bed (90 cm)', look='White walls, a timber desk under the window and a built-in wardrobe. One large window lets in morning light and faces the quiet side of the building.' WHERE code='A-101';
UPDATE rooms SET size_sqm=11, bed_type='Single bed (90 cm)', look='Same layout as the quiet single, with a small balcony door that opens onto the courtyard garden. Green outlook and the brightest light of the single rooms.' WHERE code='A-204';
UPDATE rooms SET size_sqm=18, bed_type='Two single beds', look='A long room with a bed along each side wall, a desk at the foot of each bed and a shared wardrobe by the door. The bathroom is down the corridor.' WHERE code='B-110';
UPDATE rooms SET size_sqm=26, bed_type='Four bunk-style beds', look='A wide dorm with two bunk pairs, a locker for every bed and a long shared table. Plain and practical, with a high ceiling and two windows.' WHERE code='B-215';
UPDATE rooms SET size_sqm=14, bed_type='Single bed (120 cm)', look='A private room with a wider bed, a desk by the window and a mini-fridge. The bathroom sits behind a door next to the wardrobe and has its own shower.' WHERE code='C-301';
UPDATE rooms SET size_sqm=20, bed_type='Two single beds', look='Two beds with a desk each and a private bathroom shared by the two of you. A large window fills the room with light in the afternoon.' WHERE code='C-305';
UPDATE rooms SET size_sqm=22, bed_type='Double bed (140 cm)', look='An open-plan studio: bed and desk on one side, a kitchenette with hob and sink on the other and a separate bathroom. The top-floor windows give a wide view over the campus.' WHERE code='D-401';
UPDATE rooms SET size_sqm=28, bed_type='Two single beds or one double', look='The largest studio: sleeping area, a sofa corner, a kitchenette and a private bathroom. Room for two students or one student with a second desk.' WHERE code='D-402';
